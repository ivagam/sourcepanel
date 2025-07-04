<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Domain;

class CategoryController extends Controller
{
    public function index()
    {        
        $categorys = Category::with('subcategory')->orderBy('created_at', 'desc')->get();
        return view('category.categoryList', compact('categorys'));
    }

    public function create()
    {
        $mainCategories = Category::whereNull('subcategory_id')->get();        
        $domains = Domain::orderBy('domain_name')->get();
        return view('category.addCategory', compact('domains', 'mainCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        if (is_array($domains)) {
            $domains = implode(',', $domains);
        } elseif (is_string($domains)) {
            $domains = trim($domains);
        } else {
            $domains = null;
        }

        Category::create([
            'category_name' => $request->category_name,
            'subcategory_id' => $request->subcategory_id,
            'alice_name' => $request->alice_name,
            'domains' => $domains,
            'created_by' => session('user_id'),
            'category_ids' => $request->category_ids,
        ]);

        return redirect()->route('categoryList')->with('success', 'Category added successfully.');
    }

   public function edit($id)
    {
        $editcategory = Category::findOrFail($id);
        $categorys = Category::orderBy('category_name')->get();
        $domains = Domain::orderBy('domain_name')->get();
        $mainCategories = Category::whereNull('subcategory_id')->get();

        $mainCategoryId = null;
        $chain = [];

        if(!empty($editcategory->category_ids)) {
            $chain = explode(',', $editcategory->category_ids);
            $mainCategoryId = $chain[0] ?? null;
        }

        return view('category.editCategory', compact('editcategory', 'categorys', 'domains', 'mainCategories', 'mainCategoryId'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        if (is_array($domains)) {
            $domains = implode(',', $domains);
        } elseif (is_string($domains)) {
            $domains = trim($domains);
        } else {
            $domains = null;
        }

        Category::where('category_id', $id)->update([
            'category_name' => $request->category_name,
            'subcategory_id' => $request->subcategory_id,
            'alice_name'     => $request->alice_name,
            'domains' => $domains,
            'created_by' => session('user_id'),
            'category_ids' => $request->category_ids,
        ]);

        return redirect()->route('categoryList')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        Category::where('category_id', $id)->delete();
        return redirect()->route('categoryList')->with('success', 'Category deleted successfully.');
    }

    public function getSubcategories($id)
    {
        $subcategories = Category::where('subcategory_id', $id)->get();
        return response()->json($subcategories);
    }
}
