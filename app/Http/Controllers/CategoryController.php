<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Domain;

class CategoryController extends Controller
{
    // Show only the category list
    public function index()
    {
        $categorys = Category::with('subcategory')->orderBy('created_at', 'desc')->paginate(10);
        return view('category.categoryList', compact('categorys'));
    }

    // Show Add Category form
    public function create()
    {
        $categorys = Category::orderBy('category_name')->get();
        $domains = Domain::orderBy('domain_name')->get();
        return view('category.addCategory', compact('categorys', 'domains'));
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        Category::create([
            'category_name' => $request->category_name,
            'subcategory_id' => $request->subcategory_id,
            'domains' => implode(',', $request->domains),
            'created_by' => session('user_id'),
        ]);

        return redirect()->route('categoryList')->with('success', 'Category added successfully.');
    }

    // Show Edit Category form
    public function edit($id)
    {
        $editcategory = Category::findOrFail($id);
        $categorys = Category::orderBy('category_name')->get();
        $domains = Domain::orderBy('domain_name')->get();
        return view('category.editCategory', compact('editcategory', 'categorys', 'domains'));
    }

    // Update the category
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        Category::where('category_id', $id)->update([
            'category_name' => $request->category_name,
            'subcategory_id' => $request->subcategory_id,
            'domains' => implode(',', $request->domains),
            'created_by' => session('user_id'),
        ]);

        return redirect()->route('categoryList')->with('success', 'Category updated successfully.');
    }

    // Delete the category
    public function destroy($id)
    {
        Category::where('category_id', $id)->delete();
        return redirect()->route('categoryList')->with('success', 'Category deleted successfully.');
    }
}
