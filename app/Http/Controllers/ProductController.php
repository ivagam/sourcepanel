<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Media;
use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        $mainCategory = $request->query('main_category')?$request->query('main_category'):'0';

        $totalProducts = DB::table('products')->count();
        $product = new Product();
        $product->product_name        = 'xyz ' .$totalProducts;
        $product->category_ids        = $mainCategory . ',';
        $product->category_id         = $mainCategory;
        $product->product_url         = Str::slug($product->product_name);
        $product->sku                 = 'SKU' . rand(100000, 999999);
        $product->created_by          = session('user_id');
        $product->save();

        $lastInsertedId = $product->product_id;

        return redirect()->route('editProduct', ['id' => $lastInsertedId])
                     ->with('success', 'Product created successfully!');
    }

    public function productListA(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = Product::query()
            ->select([
                'products.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, products.category_ids)
                        ) as category_name")
            ])
            ->where('products.is_updated', 0);

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, products.category_ids)", [$categoryFilter]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("MATCH(products.product_name, products.description) AGAINST(? IN BOOLEAN MODE)", [$search])
                ->orWhereRaw("LOWER(products.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.sku) LIKE ?", ['%' . $search . '%']);
            });
        }

        $products = $query->orderBy('products.created_at', 'desc')->paginate(50);

        return view('product.productListA', compact('products'));
    }

    public function productListB(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryFilter = $request->input('category_filter');

        $query = Product::query()
            ->select([
                'products.*',
                DB::raw("(SELECT GROUP_CONCAT(category_name SEPARATOR ', ') 
                        FROM category 
                        WHERE FIND_IN_SET(category.category_id, products.category_ids)
                        ) as category_name")
            ])
            ->where('products.is_updated', 1);

        if ($categoryFilter) {
            $query->whereRaw("FIND_IN_SET(?, products.category_ids)", [$categoryFilter]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("MATCH(products.product_name, products.description) AGAINST(? IN BOOLEAN MODE)", [$search])
                ->orWhereRaw("LOWER(products.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.sku) LIKE ?", ['%' . $search . '%']);
            });
        }

        $products = $query->orderBy('products.created_at', 'desc')->paginate(50);

        return view('product.productListB', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric',
            'category_id'       => 'required|integer|min:0',
            'description'       => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
            'meta_description'  => 'nullable|string',
            'domains'           => 'required|array|min:1',
            'domains.*'         => 'exists:domains,domain_id',
            'images'            => 'required',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        $product = new Product();
        $product->product_name        = $validated['product_name'];
        $product->product_price       = $validated['product_price'];
        $product->category_id         = $validated['category_id'];
        $product->description         = $validated['description'] ?? null;
        $product->meta_keywords       = $validated['meta_keywords'] ?? null;
        $product->meta_description    = $validated['meta_description'] ?? null;
        $product->domains             = implode(',', $validated['domains']);
        $product->product_url         = Str::slug($validated['product_name']);
        $product->created_by          = session('user_id');
        $product->save();

        foreach ($request->file('images') as $imageFile) {
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('uploads'), $filename);
            $filePath = $filename;

            Image::create([
                'product_id' => $product->product_id,
                'file_path'  => $filePath,
                'created_by' => session('user_id'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Product created successfully!']);
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $domains = Domain::all();
        $media = Media::all();
        $mainCategories = Category::whereNull('subcategory_id')->get();

        $selectedImages = Image::where('product_id', $id)->get();

        $isDuplicate = request()->has('duplicate');

        return view('product.editProduct', compact('product', 'categories', 'mainCategories', 'domains', 'media', 'selectedImages', 'isDuplicate'));
    }


    public function duplicateProduct($id)
    {
        $original = Product::with('images')->findOrFail($id);
        
        $newProduct = $original->replicate();        
        $newProduct->product_url = Str::slug($original->product_name) . '-' . rand(1000, 9999);
        $newProduct->created_by = session('user_id');
        $newProduct->save();

        return redirect()->route('editProduct', $newProduct->product_id)
                        ->with('success', 'Product duplicated successfully!');
    }

    public function updateProduct(Request $request, $id)
    {
        $isDuplicate = $request->query('duplicate') == 1;

        if ($isDuplicate) {
            $product = new Product();
            $product->created_at = now();
            $product->updated_at = now();

            do {
                $sku = 'sku' . rand(100000, 999999);
            } while (Product::where('sku', $sku)->exists());

            $product->sku = $sku;
        } else {
            $product = Product::findOrFail($id);

            if ($request->filled('sku')) {
                $product->sku = $request->sku;
            }
        }

        $product->product_name = $request->product_name ?? $product->product_name;
        $product->product_price = $request->product_price ?? $product->product_price;
        $product->category_id = $request->category_id ?? $product->category_id;

        if ($request->category_id != 1) {
            $product->color = $request->color ?? $product->color;
            $product->size = $request->size ?? $product->size;
        } else {
            $product->color = null;
            $product->size = null;
        }

        if (is_array($request->category_ids)) {
            $product->category_ids = implode(',', $request->category_ids) . ',';
        } else {
            $product->category_ids = ($request->category_ids ?? $product->category_ids ?? '') . ',';
        }

        $product->description = $request->description ?? $product->description;
        $product->meta_keywords = $request->meta_keywords ?? $product->meta_keywords;
        $product->meta_description = $request->meta_description ?? $product->meta_description;
        $product->domains = is_array($request->domains) ? implode(',', $request->domains) : $product->domains;
        $product->product_url = $request->product_name
            ? Str::slug($request->product_name) . '-' . rand(1000, 9999)
            : $product->product_url;

        $product->created_by = session('user_id');
        $product->created_at = now();
        $product->updated_at = now();
        $product->is_updated = $request->input('is_updated', 0);

        if (empty($product->sku)) {
            do {
                $sku = 'sku' . rand(100000, 999999);
            } while (Product::where('sku', $sku)->exists());

            $product->sku = $sku;
        }

        $product->save();

        $existingImages = $request->input('existing_images', []);

        Image::where('product_id', $product->product_id)
            ->whereNotIn('file_path', $existingImages)
            ->delete();

        foreach ($existingImages as $path) {
            if (!Image::where('product_id', $product->product_id)->where('file_path', $path)->exists()) {
                Image::create([
                    'product_id' => $product->product_id,
                    'file_path' => $path,
                    'created_by' => session('user_id'),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
        }

        return redirect()->route('productListA')->with('success', 'Product updated successfully!');
    }
    
    public function deleteProduct($product_id)
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        $isUpdated = $product->is_updated;
        Image::where('product_id', $product_id)->delete();
        $product->delete();
        $route = $isUpdated == 1 ? 'productListB' : 'productListA';

        return redirect()->route($route)->with('success', 'Product deleted successfully!');
    }
    
    public function getByCategory($id)
    {
        $page = request()->query('page', 1);
        $perPage = 30;

        $media = Media::where('category_id', $id)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['media_id', 'file_path']);

        return response()->json($media);
    }

    public function uploadTempImage(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        'product_id' => 'required|integer|exists:products,product_id',
    ]);

    if ($request->hasFile('file')) {
        $file = $request->file('file');

        // Create sanitized and unique file name
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalName);
        $filename = time() . '_' . uniqid() . '_' . $sanitizedName . '.' . $extension;

        // Create uploads folder if it doesn't exist
        $uploadPath = public_path('uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move file to uploads folder
        try {
            $file->move($uploadPath, $filename);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }

        $nextSerial = Image::where('product_id', $request->product_id)->max('serial_no') + 1;

        $image = Image::create([
            'serial_no'   => $nextSerial,
            'product_id'  => $request->product_id,
            'file_path'   => $filename, // <-- only filename here
            'created_by'  => session('user_id'),
        ]);

        return response()->json([
            'success'   => true,
            'file_path' => $image->file_path,
            'image_id'  => $image->image_id,
        ]);
    }

    return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
}


public function deleteImage(Request $request)
{
    $filePath = $request->file_path;

    $image = Image::where('file_path', $filePath)->first();
    if ($image) {
        $image->delete();
        $fullPath = public_path($filePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Image not found.']);
}

    public function updateImageOrder(Request $request)
        {
            $images = $request->images;

            foreach ($images as $img) {
                \App\Models\Image::where('image_id', $img['id'])->update(['serial_no' => $img['serial_no']]);
            }

            return response()->json(['success' => true]);
        }
}
