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
        $request->validate([
            'product_name'   => 'required|string|max:255',
            'product_price'  => 'required|numeric',
            'category'       => 'required|exists:category,category_id',
            'description'    => 'nullable|string',
            'meta_keywords'  => 'nullable|string',
            'meta_description' => 'nullable|string',
            'domains'        => 'required|array',
            'domains.*'      => 'exists:domains,domain_id',
        ]);

        $product = new Product();
        $product->product_name   = $request->product_name;
        $product->product_price  = $request->product_price;
        $product->category_id    = $request->category;
        $product->description    = $request->description;
        $product->meta_keywords  = $request->meta_keywords;
        $product->meta_description = $request->meta_description;
        $product->created_by     = session('user_id');
        $product->domains        = implode(',', $request->domains);        
        $product->product_url = Str::slug($request->product_name, '-');

        $product->save();

        $selectedImages = $request->input('selected_images');
        if (is_string($selectedImages)) {
                $selectedImages = json_decode($selectedImages, true);
            }
        
        if (is_array($selectedImages) && count($selectedImages) > 0) {
            foreach ($selectedImages as $img) {

                $cleanPath = str_replace(['../public/', 'public/'], '', $img['file_path']);

                Image::create([
                    'product_id' => $product->product_id,
                    'media_id'   => $img['id'],
                    'file_path'  => $cleanPath,
                    'created_by' => session('user_id'),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Product created successfully!');
    }

    public function editProduct($id)
{
    $product = Product::findOrFail($id);
    $categories = Category::all();
    $domains = Domain::all();
    $media = Media::all();

    // You might want to load the selected images related to the product
    $selectedImages = Image::where('product_id', $id)->get();

    return view('product.editProduct', compact('product', 'categories', 'domains', 'media', 'selectedImages'));
}


    public function updateProduct(Request $request, $id)
{
    $request->validate([
        'product_name'   => 'required|string|max:255',
        'product_price'  => 'required|numeric',
        'category'       => 'required|exists:category,category_id',
        'description'    => 'nullable|string',
        'meta_keywords'  => 'nullable|string',
        'meta_description' => 'nullable|string',
        'domains'        => 'required|array',
        'domains.*'      => 'exists:domains,domain_id',
    ]);

    $product = Product::findOrFail($id);
    $product->product_name = $request->product_name;
    $product->product_price = $request->product_price;
    $product->category_id = $request->category;
    $product->description = $request->description;
    $product->meta_keywords = $request->meta_keywords;
    $product->meta_description = $request->meta_description;
    $product->domains = implode(',', $request->domains);
    $product->product_url = Str::slug($request->product_name, '-');
    $product->created_by = session('user_id');
    $product->save();

    // Update Images: remove old images and add new selected images
    Image::where('product_id', $id)->delete();

    if ($request->has('selected_images')) {
        foreach ($request->selected_images as $img) {

            $cleanPath = preg_replace('#^(?:\.\./)*public/#', '', $img['file_path']);
            $cleanPath = preg_replace('#^\.\./#', '', $cleanPath);
            
            Image::create([
                'product_id' => $product->product_id,
                'media_id'   => $img['id'],
                'file_path'  => $cleanPath,
                'created_by' => session('user_id'),
            ]);
        }
    }

    return redirect()->route('productList')->with('success', 'Product updated successfully!');
}


    public function deleteProduct($product_id)
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();
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
