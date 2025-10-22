<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {        
        $categorys = Category::with('subcategory')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $cate = Category::with('subcategory')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($categorys as $category) {
            $category->products_count = Product::whereRaw("FIND_IN_SET(?, category_ids)", [$category->category_id])->count();
        }

        return view('category.categoryList', compact('categorys', 'cate'));
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

        $brandName     = trim($request->category_name);
        $subcategoryId = $request->subcategory_id;
        $selectedCategory = Category::find($subcategoryId);

        if ($selectedCategory) {            
            $selectedCategoryId = $selectedCategory->subcategory_id;            
        }        

        $domains = $request->domains;
        if (is_array($domains)) {
            $domains = implode(',', $domains);
        } elseif (is_string($domains)) {
            $domains = trim($domains);
        } else {
            $domains = null;
        }

        $keyword = strtolower($request->input('filter_keyword', ''));
        
        if ($selectedCategoryId == 113 || $selectedCategory->subcategory_id == 113) {         
            $category2_list = Category::where('subcategory_id', 113)->get();

            foreach ($category2_list as $cat2) {
                $newCat = Category::create([
                    'category_name' => trim($cat2->category_name . ' ' . $brandName),
                    'subcategory_id' => $cat2->category_id,
                    'alice_name'     => $request->alice_name,
                    'domains'        => $domains,
                    'created_by'     => session('user_id'),
                ]);

                $newCat->category_ids = implode(',', [
                    113,
                    $cat2->category_id,
                    $newCat->category_id
                ]);
                $newCat->save();
            }

            return redirect()->route('categoryList')->with('success', 'categories added successfully.');
        }

        if ($subcategoryId != 113 && empty($keyword)) {
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

        if (!empty($keyword)) {
            $category2_list = Category::where('subcategory_id', 113)->get();
            $created = [];

            foreach ($category2_list as $cat2) {
                $category3_list = Category::where('subcategory_id', $cat2->category_id)
                    ->whereRaw("LOWER(SUBSTRING_INDEX(category_name, ' ', -1)) = ?", [$keyword])
                    ->get();

                foreach ($category3_list as $category3) {
                    $newCategory = Category::create([
                        'category_name'  => $brandName,
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
            $parts  = explode(' ', $template->category_name, 2);
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
        $subcategories = Category::where('subcategory_id', $parentId)
            ->with('children')
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
            'category_id' => 'required|integer',
            'level'       => 'required|integer|min:1|max:3',
        ]);

        DB::beginTransaction();
        try {
            $deletedCount = 0;

            if ($request->level == 3) {
                $name = Category::where('category_id', $request->category_id)->value('category_name');

                if (!$name) {
                    DB::rollBack();
                    return redirect()->route('categoryList')->with('error', 'Selected category not found.');
                }

                $matchingIds = Category::whereRaw('LOWER(category_name) = ?', [mb_strtolower($name)])
                    ->pluck('category_id')
                    ->toArray();

                $matchingIds = array_unique($matchingIds);

                foreach ($matchingIds as $id) {
                    $deletedCount += $this->deleteRecursive($id);
                }
            } elseif ($request->level == 2) {
                $name = Category::where('category_id', $request->category_id)->value('category_name');

                if (!$name) {
                    DB::rollBack();
                    return redirect()->route('categoryList')->with('error', 'Selected category not found.');
                }

                $words = explode(' ', trim($name));
                $lastWord = strtolower(end($words));

                $matchingIds = Category::whereRaw("LOWER(SUBSTRING_INDEX(category_name, ' ', -1)) = ?", [$lastWord])
                    ->pluck('category_id')
                    ->toArray();

                $matchingIds = array_unique($matchingIds);

                foreach ($matchingIds as $id) {
                    $deletedCount += $this->deleteRecursive($id);
                }
            }else {
                    $deletedCount = $this->deleteRecursive($request->category_id);
                }

            DB::commit();
            return redirect()->route('categoryList')->with('success', "$deletedCount category(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk delete failed: '.$e->getMessage());
            return redirect()->route('categoryList')->with('error', 'Failed to delete categories. '.$e->getMessage());
        }
    }

    private function deleteRecursive($id)
    {
        $count = 0;

        $children = Category::where('subcategory_id', $id)->pluck('category_id')->toArray();

        foreach ($children as $childId) {
            $count += $this->deleteRecursive($childId);
        }

        $deleted = Category::where('category_id', $id)->delete();
        return $count + (int) $deleted;
    }

    public function filterCategory(Request $request)
    {
        $categories = Category::where('subcategory_id', 114)->get();
        return view('category.filterCategory', compact('categories'));
    }

    public function updateAliceNames(Request $request)
    {
        $aliceNames = $request->input('alice_names', []);

        foreach ($aliceNames as $categoryId => $aliceName) {
            $category = Category::find($categoryId);
            if ($category) {
                $categoryName = $category->category_name;

                Category::where('category_name', $categoryName)
                    ->update(['alice_name' => $aliceName]);
            }
        }

        return redirect()->back()->with('success', 'Category Search keyword updated successfully for all matching categories!');
    }

    public function updateAllAliceNames(Request $request)
    {
        $aliceNames = $request->input('alice_names', []);

        foreach ($aliceNames as $categoryId => $aliceName) {
            $category = Category::find($categoryId);
            if ($category) {
                $parts = explode(' ', $category->category_name);
                $labelName = trim(implode(' ', array_slice($parts, 1)));

                Category::where('category_name', 'like', "%{$labelName}")
                    ->update(['alice_name' => $aliceName]);
            }
        }

        return redirect()->back()->with('success', 'All category search keywords updated successfully for all matching labels!');
    }


}