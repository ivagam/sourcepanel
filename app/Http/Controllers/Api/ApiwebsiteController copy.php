<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ApiwebsiteController111 extends Controller
{
    public function homeProduct(Request $request)
    {
        echo "hi"; exit;
        // Default limit and page
        $limit = (int) $request->query('limit', 12);
        $page = (int) $request->query('page', 1);
        $categoryId = $request->query('category');

        $productBaseQuery = Product::query()
            ->where('is_delete', '!=', 1)
            ->where('status', 1);

        // ✅ Filter by category (numeric ID or name)
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

        // ✅ Group by product_url (unique products)
        $subQuery = $productBaseQuery
            ->select(DB::raw('MIN(product_id) as id'))
            ->groupBy('product_url');

        $totalProducts = $subQuery->count();
        $productIds = $subQuery->pluck('id')->toArray();

        // ✅ Pagination logic
        $offset = ($page - 1) * $limit;

        $products = Product::with(['images', 'category'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        // ✅ Load supporting data
        $brands = Brand::all();

        // ✅ Prepare pagination info
        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_products' => $totalProducts,
            'total_pages' => ceil($totalProducts / $limit),
        ];

        // ✅ Return JSON response
        return response()->json([
            'status' => true,
            'pagination' => $pagination,
            'products' => $products,
            'brands' => $brands
        ]);
    }
}
