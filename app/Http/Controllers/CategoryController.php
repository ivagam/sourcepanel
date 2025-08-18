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


        $domains = $request->domains;
        if (is_array($domains)) {
            $domains = implode(',', $domains);
        } elseif (is_string($domains)) {
            $domains = trim($domains);
        } else {
            $domains = null;
        }

        $keyword = strtolower($request->input('filter_keyword', ''));

        if (empty($keyword)) {
            
            Category::create([
                'category_name'  => $request->category_name,
                'subcategory_id' => $request->subcategory_id,
                'alice_name'     => $request->alice_name,
                'domains'        => $domains,
                'created_by'     => session('user_id'),
                'category_ids'   => $request->category_ids,
            ]);

            return redirect()->route('categoryList')->with('success', 'Category added successfully.');
        }

        $category2_list = Category::where('subcategory_id', 113)->get();
        $created = [];

        foreach ($category2_list as $cat2) {
            $category3_list = Category::where('subcategory_id', $cat2->category_id)
                ->whereRaw("LOWER(SUBSTRING_INDEX(category_name, ' ', -1)) = ?", [$keyword])
                ->get();

            foreach ($category3_list as $category3) {
                $newCategory = Category::create([
                    'category_name'  => $request->category_name,
                    'subcategory_id' => $category3->category_id,
                    'alice_name'     => $request->alice_name,
                    'domains'        => $domains,
                    'created_by'     => session('user_id'),
                ]);

                $newCategory->category_ids = implode(',', [
                    113,
                    $cat2->category_id,
                    $category3->category_id,
                    $newCategory->category_id
                ]);

                $newCategory->save();
                $created[] = $newCategory;
            }
        }

        return redirect()->route('categoryList')->with('success', 'Categories added successfully.');
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

}
