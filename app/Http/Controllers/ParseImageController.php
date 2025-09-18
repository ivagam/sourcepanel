<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ParseImageController extends Controller
{
    public function index()
    {
        $scrapedData = session('scrapedData', []); 
        return view('parseimage.parseImage', compact('scrapedData'));
    }

    public function parseUrl(Request $request)
    {
        $url = $request->page_url;
        $html = @file_get_contents($url);
        if (!$html) {
            return redirect()->route('parseImage.index')->with('error', "Failed to fetch URL: $url");
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $img_tags = $xpath->query("//img");

        $scrapedData = [];
        foreach ($img_tags as $img) {
            $src = $img->getAttribute('src');
            if ($src && !preg_match('/base64/', $src)) {
                $scrapedData[] = ['image' => $src];
            }
        }

        session()->flash('scrapedData', $scrapedData);
        session()->flash('page_url', $url);

        return redirect()->route('parseImage');
    }

    public function storeImages(Request $request)
    {
        $selectedImages = $request->selected_images ?? [];
        
        if (empty($selectedImages)) {
            return redirect()->back()->with('error', 'No images selected!');
        }

        $mainCategory = $request->input('main_category', 0);
        $totalProducts = Product::count();

        $product = new Product();
        $product->product_name = 'xyz ' . $totalProducts;
        $product->category_ids = $mainCategory . ',';
        $product->category_id = $mainCategory;
        $product->product_url = Str::slug($product->product_name);
        $product->sku = 'SKU' . rand(100000, 999999);
        $product->created_by = session('user_id');
        $product->seo = 0;
        $product->save();

        $productId = $product->product_id;

        foreach ($selectedImages as $imageUrl) {
            
            $imageJson = html_entity_decode($imageUrl);
            $data = json_decode($imageJson, true);
           
            if (!$data || !isset($data['image'])) continue;

            $imageUrl = $data['image'];
            
            $filename = $this->downloadImage($imageUrl);
            if (!$filename) continue;

            DB::transaction(function () use ($productId, $filename) {
                $serialNo = (Image::where('product_id', $productId)->max('serial_no') ?? 0) + 1;
                Image::create([
                    'serial_no'  => $serialNo,
                    'product_id' => $productId,
                    'file_path'  => $filename,
                    'created_by' => session('user_id'),
                ]);
            });
        }

        if (method_exists($this, 'normalizeSerials')) {
            $this->normalizeSerials($productId);
        }

        return redirect()->route('editProduct', ['id' => $productId])
                         ->with('success', 'Product created and images downloaded & stored successfully!');
    }

    private function downloadImage($imageUrl)
    {        
        try {
            $imageContent = @file_get_contents($imageUrl);
            if (!$imageContent) return null;

            $ext = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) $ext = 'jpg';

            $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 15);
            $filename = $randomString . '.' . $ext;

            $uploadPath = public_path('uploads');
            if (!File::exists($uploadPath)) File::makeDirectory($uploadPath, 0777, true);

            file_put_contents($uploadPath . '/' . $filename, $imageContent);

            return $filename;
        } catch (\Exception $e) {
            \Log::error("Image download failed: " . $e->getMessage());
            return null;
        }
    }
}
