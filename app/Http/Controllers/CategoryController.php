<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Domain;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{
    public function index()
    {
        $categorys = Category::with('subcategory')->orderBy('created_at', 'desc')->paginate(10);
        $domains = Domain::orderBy('domain_name')->get();
        return view('category.index', compact('categorys', 'domains'));
    }

public function store(Request $request)
{
     $request->validate([
        'category_name' => 'required|string|max:255',
    ]);

     Category::create([
        'category_name' => $request->category_name,
        'subcategory_id' => $request->subcategory_id,
        'domains'        => implode(',', $request->domains),
        'created_by' => session('user_id'),
    ]);

     return redirect()->route('categoryIndex')->with('success', 'Category added successfully.');
}

public function edit($id)
    {
        $editcategory = Category::findOrFail($id);
        $categorys = Category::with('subcategory')->orderBy('created_at', 'desc')->paginate(10);
        $domains = Domain::orderBy('domain_name')->get();
        return view('category.index', compact('editcategory', 'categorys', 'domains'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'category_name' => 'required|string|max:255',
    ]);

    Category::where('category_id', $id)->update([
        'category_name' => $request->category_name,
        'subcategory_id' => $request->subcategory_id,
        'domains'       => implode(',', $request->domains),
        'created_by' => session('user_id'), // optional
    ]);

    return redirect()->route('categoryIndex')->with('success', 'Category updated successfully.');
}


public function destroy($id)
{
    Category::where('category_id', $id)->delete();
    return redirect()->route('categoryIndex')->with('success', 'Category deleted successfully.');
}

}
