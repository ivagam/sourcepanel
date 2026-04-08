<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Whatsapp;
class ApiwebsiteController extends Controller
{
    public function homeProduct(Request $request)
    {        
        $limit = (int) $request->query('limit', 12);
        $page = (int) $request->query('page', 1);
        $categoryId = $request->query('category');

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);
          
        if ($categoryId) {
            if (is_numeric($categoryId)) {
                $matchingCategoryIds = Category::whereRaw("FIND_IN_SET(?, category_ids)", [$categoryId])
                    ->pluck('category_id')
                    ->toArray();

                $matchingCategoryIds[] = $categoryId;
            } else {
                $matchingCategoryIds = Category::whereRaw(
                    'LOWER(category_name) LIKE ?',
                    ['%' . strtolower($categoryId) . '%']
                )->pluck('category_id')->toArray();
            }

            if (!empty($matchingCategoryIds)) {
                $productBaseQuery->whereIn('category_id', $matchingCategoryIds);
            } else {
                $productBaseQuery->whereRaw('0 = 1');
            }
        }

        $subQuery = $productBaseQuery
            ->select(DB::raw('MIN(product_id) as id'))
            ->groupBy('product_url');

        $totalProducts = $subQuery->count();
        $productIds = $subQuery->pluck('id')->toArray();

        $offset = ($page - 1) * $limit;

        $products = Product::with(['images', 'category'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        $brands = Brand::all();

        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'products' => $products,
            'brands' => $brands
        ]);
    }

    public function galleryProduct(Request $request)
    {
        $limit = (int) $request->query('limit', 12);
        $page = (int) $request->query('page', 1);
        $categoryName = $request->query('category');

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);

        // Handle "videos" category specially
        if ($categoryName === 'videos') {
            $productBaseQuery->whereHas('images', function ($q) {
                $q->whereRaw("LOWER(SUBSTRING_INDEX(file_path, '.', -1)) IN ('mp4','webm','mov','avi')");
            });

            $totalProducts = $productBaseQuery->count();

            $offset = ($page - 1) * $limit;

            $products = $productBaseQuery
                ->with(['images' => function ($q) {
                    $q->whereRaw("LOWER(SUBSTRING_INDEX(file_path, '.', -1)) IN ('mp4','webm','mov','avi')")
                        ->orderBy('serial_no');
                }, 'category'])
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get();

        } else {
            if ($categoryName) {
                $matchingCategoryIds = Category::where('category_name', 'like', "%$categoryName%")
                    ->pluck('category_id')
                    ->toArray();

                if (!empty($matchingCategoryIds)) {
                    $productBaseQuery->where(function ($query) use ($matchingCategoryIds) {
                        foreach ($matchingCategoryIds as $catId) {
                            $query->orWhere('category_id', $catId)
                                ->orWhereRaw("FIND_IN_SET(?, category_ids)", [$catId]);
                        }
                    });
                } else {
                    $productBaseQuery->whereRaw('0 = 1');
                }
            }

            $subQuery = $productBaseQuery
                ->select(DB::raw('MIN(product_id) as id'))
                ->groupBy('product_url');

            $totalProducts = $subQuery->count();
            $productIds = $subQuery->pluck('id')->toArray();

            $offset = ($page - 1) * $limit;

            $products = Product::with(['images', 'category'])
                ->whereIn('product_id', $productIds)
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get();
        }

        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'category' => $categoryName,
            'products' => $products,
        ]);
    }

    public function gallerySearchProduct(Request $request)
    {
        $limit = (int) $request->query('limit', 12);
        $page = (int) $request->query('page', 1);
        $categoryName = $request->query('category');
        $search = strtolower($request->input('search', ''));

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);

        // Handle videos category
        if ($categoryName === 'videos') {
            $productBaseQuery->whereHas('images', function ($q) {
                $q->whereRaw("LOWER(SUBSTRING_INDEX(file_path, '.', -1)) IN ('mp4','webm','mov','avi')");
            });
        }

        // Handle other categories
        if ($categoryName && $categoryName !== 'videos') {
            $matchingCategoryIds = Category::where('category_name', 'like', "%$categoryName%")
                ->pluck('category_id')
                ->toArray();

            if (!empty($matchingCategoryIds)) {
                $productBaseQuery->where(function ($query) use ($matchingCategoryIds) {
                    foreach ($matchingCategoryIds as $catId) {
                        $query->orWhere('category_id', $catId)
                            ->orWhereRaw("FIND_IN_SET(?, category_ids)", [$catId]);
                    }
                });
            } else {
                $productBaseQuery->whereRaw('0 = 1');
            }
        }

        // Search filtering (products + categories)
        if (!empty($search)) {
            $productBaseQuery->leftJoin('category', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(category.category_id, products.category_ids)"), '>', DB::raw('0'));
            })
            ->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(products.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(category.category_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        // Build product list
        $subQuery = $productBaseQuery
            ->select(DB::raw('MIN(products.product_id) as id'))
            ->groupBy('products.product_url');

        $totalProducts = $subQuery->count();
        $productIds = $subQuery->pluck('id')->toArray();

        $offset = ($page - 1) * $limit;

        $products = Product::with(['images', 'category'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        // Pagination response
        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'category' => $categoryName,
            'search' => $search,
            'products' => $products,
        ]);
    }

    public function loadMoreProducts(Request $request)
    {
        $limit = (int) $request->input('limit', 12);
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $categoryName = $request->input('category');
        $search = strtolower($request->input('search', ''));

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);

        // 🔍 Handle search
        if (!empty($search)) {
            $productBaseQuery->leftJoin('category', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(category.category_id, products.category_ids)"), '>', DB::raw('0'));
            })
            ->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(products.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(category.category_name) LIKE ?", ['%' . $search . '%']);
            });

            // Filter by category (if given)
            if ($categoryName && $categoryName !== 'videos') {
                if (is_numeric($categoryName)) {
                    $categoryName = Category::where('category_id', $categoryName)->value('category_name');
                }

                $matchingCategoryIds = Category::where('category_name', 'like', "%$categoryName%")
                    ->pluck('category_id')
                    ->toArray();

                if (!empty($matchingCategoryIds)) {
                    $productBaseQuery->where(function ($query) use ($matchingCategoryIds) {
                        foreach ($matchingCategoryIds as $catId) {
                            $query->orWhere('products.category_id', $catId)
                                ->orWhereRaw("FIND_IN_SET(?, products.category_ids)", [$catId]);
                        }
                    });
                } else {
                    $productBaseQuery->whereRaw('0 = 1');
                }
            }

            $subQuery = $productBaseQuery
                ->select(DB::raw('MIN(products.product_id) as id'))
                ->groupBy('products.product_url');

        } elseif ($categoryName === 'videos') {
            // 🎬 Handle video category
            $productBaseQuery->whereHas('images', function ($q) {
                $q->whereRaw("LOWER(SUBSTRING_INDEX(file_path, '.', -1)) IN ('mp4','webm','mov','avi')");
            });

            $subQuery = $productBaseQuery->select(DB::raw('MIN(product_id) as id'))->groupBy('product_url');

        } else {
            // 📂 Handle normal category filtering
            if ($categoryName) {
                $matchingCategoryIds = Category::where('category_name', 'like', "%$categoryName%")
                    ->pluck('category_id')
                    ->toArray();

                if (!empty($matchingCategoryIds)) {
                    $productBaseQuery->where(function ($query) use ($matchingCategoryIds) {
                        foreach ($matchingCategoryIds as $catId) {
                            $query->orWhere('category_id', $catId)
                                ->orWhereRaw("FIND_IN_SET(?, category_ids)", [$catId]);
                        }
                    });
                } else {
                    $productBaseQuery->whereRaw('0 = 1');
                }
            }

            $subQuery = $productBaseQuery
                ->select(DB::raw('MIN(product_id) as id'))
                ->groupBy('product_url');
        }

        // 🧮 Pagination & Fetch
        $totalProducts = $subQuery->count();
        $productIds = $subQuery->skip($offset)->take($limit)->pluck('id')->toArray();

        $products = Product::with(['images', 'category'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->get();

        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'category' => $categoryName,
            'search' => $search,
            'products' => $products,
        ]);
    }
    public function liveSearchProduct(Request $request)
    {
        $limit = (int) $request->query('limit', 12);
        $page = (int) $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $search = strtolower($request->input('search', ''));
        $categoryId = $request->input('category');

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);

        // 🔍 Apply search filter
        if (!empty($search)) {
            $productBaseQuery->leftJoin('category', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(category.category_id, products.category_ids)"), '>', DB::raw('0'));
            })
            ->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(products.product_name) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.description) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(products.sku) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(category.category_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        // 🏷️ Filter by category ID if provided
        if (!empty($categoryId)) {
            $productBaseQuery->whereRaw("FIND_IN_SET(?, products.category_ids)", [$categoryId]);
        }

        // 🧮 Group by product_url to remove duplicates
        $subQuery = $productBaseQuery
            ->select(DB::raw('MIN(products.product_id) as id'))
            ->groupBy('products.product_url');

        $totalProducts = $subQuery->count();
        $productIds = $subQuery->skip($offset)->take($limit)->pluck('id')->toArray();

        // 📦 Fetch products with relationships
        $products = Product::with(['images', 'category'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->get();

        // 🏷️ Optionally include additional data (for frontend filters)
        $categories = Category::with('children')
            ->whereNull('subcategory_id')
            ->get();

        $brands = Brand::all();
        $banners = Banner::all();

        // 📄 Pagination metadata
        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        // ✅ JSON API response
        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'search' => $search,
            'category_id' => $categoryId,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'banners' => $banners,
        ]);
    }

    public function msgList(Request $request)
    {
        $messages = Whatsapp::all();

        return response()->json([
            'status' => true,
            'total' => $messages->count(),
            'messages' => $messages,
        ]);
    }

}
