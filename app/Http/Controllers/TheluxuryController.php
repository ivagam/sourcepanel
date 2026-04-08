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
use App\Models\ScrapeUrl;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class TheluxuryController extends Controller
{
    
    public function theluxuryListA(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = ScrapeProduct::query()
            ->select([
                'scrape_product.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, scrape_product.category_ids)
                        ) as category_name"),
                DB::raw("(SELECT COUNT(*) FROM scrape_images WHERE scrape_images.scrape_product_id = scrape_product.scrape_product_id) as image_count")
            ])
            ->where('scrape_product.is_updated', 0)
            ->where('scrape_product.is_product_c', '!=', 1);

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, scrape_product.category_ids)", [$categoryFilter]);
        }

        if (!empty($search)) {
            $keywords = preg_split('/\s+/', $search);

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $q->where(function ($sub) use ($word) {
                            $sub->whereRaw("LOWER(scrape_product.product_name) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.description) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.sku) LIKE ?", ["%$word%"]);
                        });
                    }
                }
            });
        }

        $theluxury = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);

        return view('theluxury.theluxuryListA', compact('theluxury'));
    }

    public function theluxuryListB(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = ScrapeProduct::query()
            ->select([
                'scrape_product.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, scrape_product.category_ids)
                        ) as category_name"),
                DB::raw("(SELECT COUNT(*) FROM scrape_images WHERE scrape_images.scrape_product_id = scrape_product.scrape_product_id) as image_count")
            ])
            ->where('scrape_product.is_updated', 1)
            ->where('scrape_product.is_product_c', '!=', 1);

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, scrape_product.category_ids)", [$categoryFilter]);
        }

        // ✅ REPLACED SEARCH LOGIC
        if (!empty($search)) {
            $keywords = preg_split('/\s+/', $search);

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $q->where(function ($sub) use ($word) {
                            $sub->whereRaw("LOWER(scrape_product.product_name) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.description) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.sku) LIKE ?", ["%$word%"]);
                        });
                    }
                }
            });
        }

        $theluxury = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);

        return view('theluxury.theluxuryListB', compact('theluxury'));
    }

    public function theluxuryListC(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = ScrapeProduct::query()
            ->select([
                'scrape_product.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, scrape_product.category_ids)
                        ) as category_name"),
                DB::raw("(SELECT COUNT(*) FROM scrape_images WHERE scrape_images.scrape_product_id = scrape_product.scrape_product_id) as image_count")
            ])
            ->where('scrape_product.is_updated', 0)
            ->where('scrape_product.is_product_c', '!=', 0);

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, scrape_product.category_ids)", [$categoryFilter]);
        }

        // ✅ REPLACED SEARCH LOGIC
        if (!empty($search)) {
            $keywords = preg_split('/\s+/', $search);

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $q->where(function ($sub) use ($word) {
                            $sub->whereRaw("LOWER(scrape_product.product_name) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.description) LIKE ?", ["%$word%"])
                                ->orWhereRaw("LOWER(scrape_product.sku) LIKE ?", ["%$word%"]);
                        });
                    }
                }
            });
        }

        $theluxury = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);

        return view('theluxury.theluxuryListC', compact('theluxury'));
    }

    public function editTheluxury($id)
    {
        $product = ScrapeProduct::findOrFail($id);
        $categories = Category::all();
        $domains = Domain::all();
        $media = Media::all();
        $mainCategories = Category::whereNull('subcategory_id')->get();

        $selectedImages = ScrapeImage::where('scrape_product_id', $id)->get();

        $isDuplicate = request()->has('duplicate');

        return view('theluxury.editTheluxury', compact('product', 'categories', 'mainCategories', 'domains', 'media', 'selectedImages', 'isDuplicate'));
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

    public function duplicateTheluxuryProduct($id)
    {
        $original = ScrapeProduct::with('images')->findOrFail($id);
        
        $newProduct = $original->replicate();   
        $newProduct->product_name = $original->product_name . ' ' . rand(10, 99);
        $newProduct->product_url = Str::slug($original->product_name) . '-' . rand(1000, 9999);
        $newProduct->created_by = session('user_id');
        $newProduct->save();

        foreach ($original->images as $image) {
            $newImage = $image->replicate();
            $newImage->scrape_product_id = $newProduct->scrape_product_id;
            $newImage->save();
        }

        return redirect()->route('editTheluxury', $newProduct->scrape_product_id)
                        ->with('success', 'Theluxury Product duplicated successfully!');
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

    public function scrapeUrl ()
    {
        $scrapeUrl = ScrapeUrl::orderBy('created_at', 'asc')->paginate(50);
        
        return view('scrape.scrapeUrl', compact('scrapeUrl'));        
    }

    public function destroy($id)
    {
        ScrapeUrl::where('id', $id)->delete();
        return redirect()->route('scrapeUrl')->with('success', 'ScrapeUrl deleted successfully.');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->ids; // array of selected IDs
        if ($ids) {
            ScrapeUrl::whereIn('id', $ids)->delete();
            return redirect()->route('scrapeUrl')->with('success', 'Selected ScrapeUrls deleted successfully.');
        }
        return redirect()->route('scrapeUrl')->with('success', 'No URLs selected.');
    }

    public function theluxuryList(Request $request)
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

        $theluxury = $query->orderBy('scrape_product.created_at', 'desc')->paginate(50);

        return view('theluxury.theluxuryList', compact('theluxury'));
    }

    public function searchtheluxury(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryId = $request->input('category_id');

        $query = Product::query()
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
        
        return view('theluxury.theluxuryResults', compact('products'));
    }

    public function updateTheluxuryProduct(Request $request, $id)
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

        $oldName = $product->product_name;

        $product->product_name = $request->product_name ?? $product->product_name;
        $product->product_price = $request->product_price ?? $product->product_price;
        $product->category_id = $request->category_id ?? $product->category_id;

        if ($request->category_id != 1) {
            $product->color = $request->color ?? $product->color;
            $product->size = $request->size ?? null;
        } else {
            $product->color = null;
            $product->size = null;
        }

        if (is_array($request->category_ids)) {
            $product->category_ids = implode(',', $request->category_ids) . ',';
        } else {
            $product->category_ids = ($request->category_ids ?? $product->category_ids ?? '') . ',';
        }

        $content = trim($request->description_en);

        if ($content === '<p><br></p>' || $content === '<p></p>') {
            $content = null;
        }

        $product->description = $content;

        $product->chinese_description = $request->chinese_description ?? '';

        $product->meta_keywords = $request->meta_keywords ?? '';
        $product->meta_description = $request->meta_description ?? '';

        $product->purchase_value = $request->filled('purchase_value') 
            ? $request->purchase_value 
            : 715;

        $product->purchase_code = $request->purchase_code ?? $product->purchase_code;
        $product->note = $request->note ?? '';
        $product->domains = is_array($request->domains) ? implode(',', $request->domains) : $product->domains;

        $product->created_by = session('user_id');
        $product->created_at = now();
        $product->updated_at = now();

        $product->is_updated = $request->input('is_updated', 0);

        $product->status = ($request->input('is_updated', 0) != 0 || $request->has('is_product_c')) ? 1 : 0;

        if (Str::contains(strtolower($oldName), 'xyz')) {
            if (empty($product->sku)) {
                do {
                    $sku = 'sku' . rand(100000, 999999);
                } while (ScrapeProduct::where('sku', $sku)->exists());

                $product->sku = $sku;
            }

            $url = $product->sku . '-' . Str::slug($product->product_name) . '-' . rand(1000, 9999);
            $product->product_url = str_replace([',', "'", '"'], '', $url);
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

                $isVideo = preg_match('/\.(mp4|webm|ogg)$/i', $path);

                if ($isVideo) {
                    $serialNo = (ScrapeImage::where('scrape_product_id', $product->scrape_product_id)->max('serial_no') ?? 0) + 1000;
                } else {
                    $serialNo = (ScrapeImage::where('scrape_product_id', $product->scrape_product_id)->max('serial_no') ?? 0) + 1;
                }

                ScrapeImage::create([
                    'scrape_product_id' => $product->scrape_product_id,
                    'file_path' => $path,
                    'serial_no' => $serialNo,
                    'created_by' => session('user_id'),
                ]);
            }
        }

        if ($request->expectsJson()) {            
            return response()->json(['success' => true, 'message' => 'Theluxury Product updated successfully!']);
        }

        return redirect()->route('theluxuryList')->with('success', 'Theluxury Product updated successfully!');
    }

}
