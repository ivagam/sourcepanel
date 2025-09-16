<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scrape;
use App\Models\ScrapeProduct;
use App\Models\ScrapeImage;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ScrapeController extends Controller
{
    public function index()
    {
        $scrape = Scrape::where('status', 0)->first();
        if (!$scrape) {
            return "No URLs to scrape.";
        }

        $url = $scrape->url;

        $html = @file_get_contents($url);
        if (!$html) {
            return "Failed to fetch URL: $url";
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $h1_tags = $dom->getElementsByTagName('h1');
        $product_name = $h1_tags->length > 0 ? trim($h1_tags->item(0)->textContent) : 'Unnamed Product';

        $description = '';
        $p_tags = $dom->getElementsByTagName('p');
        $p_count = $p_tags->length;
        for ($i = 0; $i < $p_count - 1; $i++) {
                $text = $p_tags->item($i)->textContent;                
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');                
                $text = trim($text);                
                $text = preg_replace('/\s+/', ' ', $text);                
                $description .= $text . "\n";
            }

        $sku = 'SKU' . rand(100000, 999999);

        $slug = Str::slug($product_name);
        $randomNumber = rand(1000, 9999);
        $product_url = $slug . '_' . $randomNumber;

        $size = 'S,L,M,XL,XXL';

        $product = ScrapeProduct::create([
            'scrape_id' => $scrape->scrape_id,
            'product_name' => $product_name,
            'description' => $description,
            'size' => $size,
            'sku' => $sku,
            'product_url' => $product_url,
            'created_at' => now(),
        ]);

        $maxSerial = ScrapeImage::where('scrape_product_id', $product->scrape_product_id)->max('serial_no') ?? 0;

        $div_tags = $dom->getElementsByTagName('div');
        foreach ($div_tags as $div) {
            $class = $div->getAttribute('class');
            if ($class === 'separator') {
                $imgs = $div->getElementsByTagName('img');
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    if ($src) {
                        $savedFileName = $this->downloadImage($src);
                        if ($savedFileName) {
                            $maxSerial++;
                            ScrapeImage::create([                                
                                'scrape_product_id' => $product->scrape_product_id,
                                'file_path' => $savedFileName,
                                'serial_no' => $maxSerial,
                                'created_by'  => session('user_id'),
                            ]);
                        }
                    }
                }
            }
        }

        // Update scrape status
        $scrape->status = 1;
        $scrape->save();

        return "Scraping completed for URL: $url";
    }

    public function updateScrapeProduct(Request $request, $id)
    {        
        $isDuplicate = $request->query('duplicate') == 1;

        if ($isDuplicate) {
            $product = new ScrapeProduct();
            $product->created_at = now();
            $product->updated_at = now();

            do {
                $sku = 'sku' . rand(100000, 999999);
            } while (ScrapeProduct::where('sku', $sku)->exists());

            $product->sku = $sku;
        } else {
            $product = ScrapeProduct::findOrFail($id);

            if ($request->filled('sku')) {
                $product->sku = $request->sku;
            }
        }

        $product->product_name = $request->product_name ?? $product->product_name;
        $product->product_price = $request->product_price ?? $product->product_price;
        $product->category_id = $request->category_id ?? $product->category_id;

        if ($request->category_id != 1) {
            $product->color = $request->color ?? $product->color;
            $product->size = $request->size ?? ',';
        } else {
            $product->color = null;
            $product->size = null;
        }

        if (is_array($request->category_ids)) {
            $product->category_ids = implode(',', $request->category_ids) . ',';
        } else {
            $product->category_ids = ($request->category_ids ?? $product->category_ids ?? '') . ',';
        }

        $oldName = $product->product_name;

        $product->description = $request->description ?? $product->description;
        $product->meta_keywords = $request->meta_keywords ?? $product->meta_keywords;
        $product->meta_description = $request->meta_description ?? $product->meta_description;
        $product->purchase_value = $request->purchase_value ?? $product->purchase_value;
        $product->purchase_code = $request->purchase_code ?? $product->purchase_code;
        $product->note = $request->note ?? $product->note;
        $product->domains = is_array($request->domains) ? implode(',', $request->domains) : $product->domains;        
        $product->created_by = session('user_id');
        $product->created_at = now();
        $product->updated_at = now();
        $product->is_updated = $request->input('is_updated', 0);

        if ($request->filled('product_name') && $request->product_name !== $oldName) {
            $product->product_url = Str::slug($request->product_name) . '-' . rand(1000, 9999);
        }

        if (empty($product->sku)) {
            do {
                $sku = 'sku' . rand(100000, 999999);
            } while (ScrapeProduct::where('sku', $sku)->exists());

            $product->sku = $sku;
        }

        if ($request->input('is_updated') == 1) {
            $product->is_product_c = 0;
        } else {
            $product->is_product_c = $request->has('is_product_c') ? 1 : 0;
        }

        $product->save();

        $existingImages = $request->input('existing_images', []);

        ScrapeImage::where('scrape_product_id', $product->scrape_product_id)
            ->whereNotIn('file_path', $existingImages)
            ->delete();

        foreach ($existingImages as $path) {
            if (!ScrapeImage::where('scrape_product_id', $product->scrape_product_id)->where('file_path', $path)->exists()) {
                ScrapeImage::create([
                    'scrape_product_id' => $product->scrape_product_id,
                    'file_path' => $path,
                    'created_by' => session('user_id'),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Scrape Product updated successfully!']);
        }

        return redirect()->route('scrapeList')->with('success', 'Scrape Product updated successfully!');
    }

    private function downloadImage($imageUrl)
    {
        try {
            $imageContent = @file_get_contents($imageUrl);
            if (!$imageContent) {
                return null;
            }

            $ext = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) {
                $ext = 'jpg';
            }

            $randomString = substr(md5(uniqid(mt_rand(), true)), 0, 3);

            $filename = time() . '_' . $randomString . '.' . $ext;

            $uploadPath = public_path('uploads');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true);
            }

            $filePath = $uploadPath . '/' . $filename;
            file_put_contents($filePath, $imageContent);

            return $filename;

        } catch (\Exception $e) {
            \Log::error("Image download failed: " . $e->getMessage());
            return null;
        }
    }

    public function scrapeList(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = ScrapeProduct::query()
            ->select([
                'scrape_product.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, scrape_product.category_ids)
                        ) as category_name")
            ]);            

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, scrape_product.category_ids)", [$categoryFilter]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("MATCH(scrape_product.product_name, scrape_product.description) AGAINST(? IN BOOLEAN MODE)", [$search])
                ->orWhereRaw("LOWER(scrape_product.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(scrape_product.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(scrape_product.sku) LIKE ?", ['%' . $search . '%']);
            });
        }

        $scrape = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);


        return view('scrape.scrapeList', compact('scrape'));
    }

    public function editScrape($id)
    {
        $product = ScrapeProduct::findOrFail($id);
        $categories = Category::all();
        $domains = Domain::all();
        $media = Media::all();
        $mainCategories = Category::whereNull('subcategory_id')->get();

        $selectedImages = ScrapeImage::where('scrape_product_id', $id)->get();

        $isDuplicate = request()->has('duplicate');

        return view('scrape.editScrape', compact('product', 'categories', 'mainCategories', 'domains', 'media', 'selectedImages', 'isDuplicate'));
    }

    public function updateScrapeImageOrder(Request $request)
    {
        $images = $request->images;

        foreach ($images as $img) {
            \App\Models\ScrapeImage::where('id', $img['id'])->update(['serial_no' => $img['serial_no']]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadScrapeTempImage(Request $request)
    {        
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'scrape_product_id' => 'required|integer|exists:scrape_product,scrape_product_id',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalName);
            $filename = time() . '_' . uniqid() . '_' . $sanitizedName . '.' . $extension;

            $uploadPath = public_path('uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $filename);

            $image = DB::transaction(function () use ($request, $filename) {
                $maxSerial = ScrapeImage::where('scrape_product_id', $request->scrape_product_id)
                            ->lockForUpdate()
                            ->max('serial_no') ?? 0;

                $serialNo = $maxSerial + 1;

                return ScrapeImage::create([
                    'serial_no' => $serialNo,
                    'scrape_product_id' => $request->scrape_product_id,
                    'file_path' => $filename,
                    'created_by' => session('user_id'),
                ]);
            });

            $this->normalizeSerials($request->scrape_product_id);

            return response()->json([
                'success' => true,
                'file_path' => $image->file_path,
                'id' => $image->id,
                'serial_no' => $image->serial_no,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    public function deleteScrapeProduct($scrape_product_id)
    {        
        $product = ScrapeProduct::where('scrape_product_id', $scrape_product_id)->firstOrFail();

        $isUpdated = $product->is_updated;
        ScrapeImage::where('scrape_product_id', $scrape_product_id)->delete();
        $product->delete();
        
        return redirect()->route('scrapeList')->with('success', 'Scrape Product deleted successfully!');
    }

    public function deleteScrapeImage(Request $request)
    {
        
        $imageId = $request->id;
        $image = ScrapeImage::find($imageId);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        $fullPath = public_path($image->file_path);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        } else {
            $storagePath = storage_path('app/' . $image->file_path);
            if (file_exists($storagePath)) {
                @unlink($storagePath);
            }
        }

        $image->delete();

        return response()->json(['success' => true]);
    }

    private function normalizeSerials($productId)
    {
        $images = ScrapeImage::where('scrape_product_id', $productId)
            ->orderByRaw("CASE 
                WHEN file_path REGEXP '\\.(mp4|mov|avi|webm)$' THEN 2 
                ELSE 1 END")
            ->orderBy('serial_no')
            ->get();

        foreach ($images as $index => $img) {
            $img->serial_no = $index + 1;
            $img->save();
        }
    }

    public function duplicateScrapeProduct($id)
    {
        $original = ScrapeProduct::with('images')->findOrFail($id);
        
        $newProduct = $original->replicate();        
        $newProduct->product_url = Str::slug($original->product_name) . '-' . rand(1000, 9999);
        $newProduct->created_by = session('user_id');
        $newProduct->save();

        foreach ($original->images as $image) {
            $newImage = $image->replicate();
            $newImage->scrape_product_id = $newProduct->scrape_product_id;
            $newImage->save();
        }

        return redirect()->route('editScrape', $newProduct->scrape_product_id)
                        ->with('success', 'Scrape Product duplicated successfully!');
    }

    public function bulkUpdateScrapeSku(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No products selected.'], 400);
        }

        $newSku = 'SKU' . rand(100000, 999999);

        ScrapeProduct::whereIn('scrape_product_id', $ids)->update([
            'sku' => $newSku
        ]);

        return response()->json([
            'message' => 'Updated SKU to ' . $newSku . ' for ' . count($ids) . ' products.'
        ]);
    }

    public function searchscrape(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryId = $request->input('category_id');

        $query = ScrapeProduct::query()
            ->select([
                'scrape_product.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, scrape_product.category_ids)
                        ) as category_name")
            ]);

        if ($categoryId) {
            $query->whereRaw("FIND_IN_SET(?, scrape_product.category_ids)", [$categoryId]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(scrape_product.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(scrape_product.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(scrape_product.sku) LIKE ?", ['%' . $search . '%']);
            });
        }

        $products = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);
        
        return view('scrape.searchResults', compact('products'));
    }

}
