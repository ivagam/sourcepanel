import os
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin
import random, re
import mysql.connector

# --- DB connection ---
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        port=3306,
        user="root",
        password="",
        database="source_panel"
    )

# --- Helper: download file ---
def download_file(url, folder=r"D:\xampp82\htdocs\sourcepanel\public\uploads"):
    try:
        os.makedirs(folder, exist_ok=True)
        filename = os.path.basename(url.split("?")[0])
        filepath = os.path.join(folder, filename)

        r = requests.get(url, stream=True, timeout=10)
        if r.status_code == 200:
            with open(filepath, "wb") as f:
                f.write(r.content)
            return filename  # ✅ only return filename for DB
    except Exception as e:
        print("Error downloading", url, "->", e)
    return None

# --- Scraper ---
def scrape_urls():
    db = get_db_connection()
    cur = db.cursor(dictionary=True)

    # ✅ Fetch pending URLs
    cur.execute("SELECT id, url FROM website_links WHERE product_status = 0 LIMIT 25")
    scrapes = cur.fetchall()

    if not scrapes:
        print("No URLs to scrape.")
        return

    for scrape in scrapes:
        sid, url = scrape["id"], scrape["url"]
        print(f"\n🔗 Scraping: {url}")
        try:
            response = requests.get(url, timeout=10)
            if response.status_code != 200:
                print("❌ Failed to load:", url)
                cur.execute("UPDATE website_links SET product_status = 2 WHERE id = %s", (sid,))
                db.commit()
                continue

            soup = BeautifulSoup(response.text, "lxml")
            h1 = soup.find("h1")
            product_name = h1.text.strip() if h1 else f"xyz_{random.randint(1000,9999)}"

            paragraphs = soup.find_all("p")
            description = "\n".join(p.get_text(strip=True) for p in paragraphs)

            sku = "SKU" + str(random.randint(100000, 999999))
            slug = re.sub(r'[^a-z0-9]+', '-', product_name.lower()).strip('-')
            product_url = f"{slug}_{random.randint(1000,9999)}"
            size = "S,L,M,XL,XXL"

            found_files = []

            # --- MEDIA EXTRACTION ---
            for div in soup.find_all("div", class_="separator"):
                # --- IMAGES ---
                for img in div.find_all("img"):
                    src = img.get("src") or img.get("data-src")
                    if src:
                        full_url = urljoin(url, src)
                        filename = download_file(full_url)
                        if filename:
                            found_files.append(filename)

                # --- VIDEOS ---
                for video in div.find_all("video"):
                    src = video.get("src")
                    if not src:
                        source_tag = video.find("source")
                        if source_tag:
                            src = source_tag.get("src")
                    if src:
                        full_url = urljoin(url, src)
                        found_files.append(full_url)  # store video URL directly

                # --- IFRAMES ---
                for iframe in div.find_all("iframe"):
                    src = iframe.get("src")
                    if src:
                        full_url = urljoin(url, src)
                        found_files.append(full_url)

            # --- SAVE TO DATABASE ---
            if found_files:
                # Insert product
                cur.execute("""
                    INSERT INTO scrape_product (scrape_id, product_name, description, category_id, category_ids, size, sku, product_url, created_at)
                    VALUES (%s,%s,%s,%s,%s,%s,%s,%s,NOW())
                """, (sid, product_name, description, 113, "113,", size, sku, product_url))
                db.commit()
                product_id = cur.lastrowid

                # Insert media
                serial = 0
                for file in found_files:
                    serial += 1
                    cur.execute("""
                        INSERT INTO scrape_images (scrape_product_id, file_path, serial_no, created_by)
                        VALUES (%s,%s,%s,1)
                    """, (product_id, file, serial))
                db.commit()

                cur.execute("UPDATE website_links SET product_status = 1 WHERE id = %s", (sid,))
                db.commit()
                print(f"✅ Created product {product_name} with {len(found_files)} media files.")
            else:
                cur.execute("UPDATE website_links SET product_status = 2 WHERE id = %s", (sid,))
                db.commit()
                print("⚠️ No media found, marked as failed.")

        except Exception as e:
            print("Error scraping", url, "->", e)
            cur.execute("UPDATE website_links SET product_status = 2 WHERE id = %s", (sid,))
            db.commit()

    db.close()
    print("\n✅ Scraping completed.")

# --- Run ---
if __name__ == "__main__":
    scrape_urls()
