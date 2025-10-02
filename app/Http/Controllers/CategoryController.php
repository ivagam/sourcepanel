<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {        
        $categorys = Category::with('subcategory')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($categorys as $category) {
            $category->products_count = Product::whereRaw("FIND_IN_SET(?, category_ids)", [$category->category_id])->count();
        }

        return view('category.categoryList', compact('categorys'));
    }

    public function create(Request $request)
    {
        $mainCategories = Category::whereNull('subcategory_id')->get();        
        $domains = Domain::orderBy('domain_name')->get();
        $defaultMainCategory = $request->input('main_category', '');
        return view('category.addCategory', compact('domains', 'mainCategories', 'defaultMainCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name'  => 'required|string|max:255',
            'filter_keyword' => 'nullable|string',
        ]);

        $brandName    = trim($request->category_name);
        $subcategoryId = $request->subcategory_id;

        $domains = $request->domains;
        if (is_array($domains)) {
            $domains = implode(',', $domains);
        } elseif (is_string($domains)) {
            $domains = trim($domains);
        } else {
            $domains = null;
        }

        if ($subcategoryId != 113) {
            Category::create([
                'category_name'  => $brandName,
                'subcategory_id' => $subcategoryId,
                'alice_name'     => $request->alice_name,
                'domains'        => $domains,
                'created_by'     => session('user_id'),
                'category_ids'   => $request->category_ids,
            ]);

            return redirect()->route('categoryList')->with('success', 'Category added successfully.');
        }

        $brandCategory = Category::create([
            'category_name'  => $brandName,
            'subcategory_id' => 113,
            'alice_name'     => $request->alice_name,
            'domains'        => $domains,
            'created_by'     => session('user_id'),
            'category_ids'   => '113',
        ]);

        $brandCategory->category_ids = "113," . $brandCategory->category_id;
        $brandCategory->save();

        $templateId = 114;
        $templates = Category::where('subcategory_id', $templateId)->get();

        foreach ($templates as $template) {
            $parts = explode(' ', $template->category_name, 2);
            $suffix = isset($parts[1]) ? $parts[1] : '';
            $newName = trim($brandName . ' ' . $suffix);

            $newCategory = Category::create([
                'category_name'  => $newName,
                'subcategory_id' => $brandCategory->category_id,
                'alice_name'     => $request->alice_name,
                'domains'        => $domains,
                'created_by'     => session('user_id'),
                'category_ids'   => '',
            ]);

            $newCategory->category_ids = implode(',', [
                113,
                $brandCategory->category_id,
                $newCategory->category_id
            ]);
            $newCategory->save();

            $children = Category::where('subcategory_id', $template->category_id)->get();
            foreach ($children as $child) {
                $childNew = Category::create([
                    'category_name'  => $child->category_name,
                    'subcategory_id' => $newCategory->category_id,
                    'alice_name'     => $request->alice_name,
                    'domains'        => $domains,
                    'created_by'     => session('user_id'),
                    'category_ids'   => '',
                ]);

                $childNew->category_ids = implode(',', [
                    113,
                    $brandCategory->category_id,
                    $newCategory->category_id,
                    $childNew->category_id
                ]);
                $childNew->save();
            }
        }

        return redirect()->route('categoryList')->with('success', 'Brand categories created successfully.');
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

        $categoryName = trim($request->category_name);
        $subcategoryId = $request->subcategory_id;

        $exists = Category::where('category_name', $categoryName)
                        ->where('subcategory_id', $subcategoryId)
                        ->where('category_id', '!=', $id)
                        ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This category name already exists under the selected subcategory.');
        }
        
        $domains = $request->domains;

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

    public function getWatchSubcategories($parentId)
    {
        $subcategories = Category::where('subcategory_id', $parentId) // FIXED: Correct field
            ->with('children') // You already have 'children' relationship using 'subcategory_id'
            ->get();

        return response()->json($subcategories);
    }

    public function bulkEditCategory(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string'
        ]);

        $oldName = strtolower($request->old_name);

        $updated = Category::whereRaw('LOWER(category_name) = ?', [$oldName])
            ->update(['category_name' => $request->new_name]);
        
        return redirect()->route('categoryList')->with('success', "$updated category(s) updated successfully.");
    }

    public function bulkDeleteCategory(Request $request)
    {
        $request->validate([
            'delete_name' => 'required|string'
        ]);

        $deleteName = strtolower($request->delete_name);

        $deleted = Category::whereRaw('LOWER(category_name) = ?', [$deleteName])
            ->delete();
        
        return redirect()->route('categoryList')->with('success', "$deleted category(s) deleted successfully.");
    }

}