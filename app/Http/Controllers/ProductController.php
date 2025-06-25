<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Media;
use App\Models\Image;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function addProduct()
    {
        $media = Media::all();
        $categories = Category::all();
        $domains = Domain::orderBy('domain_name')->get();
        return view('product.addProduct', compact('categories', 'domains', 'media'));
    }

    public function productList()
    {
        $products = Product::join('category', 'products.category_id', '=', 'category.category_id')
            ->select('products.*', 'category.category_name')
            ->paginate(10);
        return view('product.productList', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric',
            'category_id'       => 'required|exists:category,category_id',
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
            $filePath = 'uploads/' . $filename;

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

        $selectedImages = Image::where('product_id', $id)->get();

        return view('product.editProduct', compact('product', 'categories', 'domains', 'media', 'selectedImages'));
    }


    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric',
            'category'          => 'required|exists:category,category_id',
            'description'       => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
            'meta_description'  => 'nullable|string',
            'domains'           => 'required|array',
            'domains.*'         => 'exists:domains,domain_id',
            'images.*'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:20480', // max 20MB per file
            'existing_images'   => 'nullable|array',
            'existing_images.*' => 'string',
        ]);

        $product = Product::findOrFail($id);

        $product->product_name = $request->product_name;
        $product->product_price = $request->product_price;
        $product->category_id = $request->category;
        $product->description = $request->description;
        $product->meta_keywords = $request->meta_keywords;
        $product->meta_description = $request->meta_description;
        $product->domains = implode(',', $request->domains);
        $product->product_url = Str::slug($request->product_name);
        $product->created_by = session('user_id');
        $product->save();

        $existingImages = $request->input('existing_images', []);

        Image::where('product_id', $product->product_id)
            ->whereNotIn('file_path', $existingImages)
            ->delete();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();            
                $path = $file->storeAs('uploads', $filename, 'public');

                Image::create([
                    'product_id' => $product->product_id,
                    'file_path'  => 'uploads/' . $filename,
                    'created_by' => session('user_id'),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!'
            ]);
        }

        return redirect()->route('productList')->with('success', 'Product updated successfully!');
    }


    public function deleteProduct($product_id)
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();
            Image::where('product_id', $product_id)->delete();
        $product->delete();
        return redirect()->route('productList')->with('success', 'Product deleted successfully!');
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
}
