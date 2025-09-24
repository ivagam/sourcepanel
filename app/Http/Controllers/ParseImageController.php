<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ParseImageController extends Controller
{

    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('EXTRACT_PICS_API_KEY');
    }


    public function index()
    {
        $scrapedData = session('scrapedData', []); 
        return view('parseimage.parseImage', compact('scrapedData'));
    }

    public function parseUrl(Request $request)
    {
        $url = $request->page_url;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.extract.pics/v0/extractions', [
            'url' => $url,
        ]);

        if ($response->failed()) {
            return redirect()->route('parseImage')->with('error', 'Failed to start extraction.');
        }

        $data = $response->json()['data'] ?? null;
        if (!$data) {
            return redirect()->route('parseImage')->with('error', 'No data returned from API.');
        }

        $extractionId = $data['id'];

        $status = $data['status'];
        $imageUrls = [];

        while ($status !== 'done' && $status !== 'error') {

            $check = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("https://api.extract.pics/v0/extractions/{$extractionId}");

            if ($check->failed()) {
                break;
            }

            $checkData = $check->json()['data'] ?? null;
            $status = $checkData['status'] ?? 'error';

            if ($status === 'done') {
                $imageUrls = $checkData['images'] ?? [];
            }
        }

        if (empty($imageUrls)) {
            return redirect()->route('parseImage')->with('error', 'No images found.');
        }

        $scrapedData = array_map(fn($img) => ['image' => $img['url']], $imageUrls);

        return redirect()->route('parseImage')->with([
            'scrapedData' => $scrapedData,
            'page_url' => $url
        ]);
    }

    public function storeImages(Request $request)
    {
        $selectedImages = $request->selected_images ?? [];
        
        if (empty($selectedImages)) {
            return redirect()->back()->with('error', 'No images selected!');
        }

        
        $totalProducts = Product::count();

        $product = new Product();
        $product->product_name = 'xyz ' . $totalProducts;        
        $product->category_id = '113';
        $product->category_ids        = 113 . ',';
        $product->product_url = Str::slug($product->product_name);
        $product->sku = 'SKU' . rand(100000, 999999);
        $product->created_by = session('user_id');
        $product->seo = 0;
        $product->size = '25cms';
        $product->purchase_value = '715';
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

    private function fetchHtml($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }
}
