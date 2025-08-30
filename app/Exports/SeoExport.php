<?php 

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SeoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $products = Product::where('seo', 0)
            ->limit(500)
            ->get(['product_id', 'product_name', 'category_id']);

        return $products->map(function ($product) {            
            $category = Category::where('category_id', $product->category_id)->value('category_name');

            return [
                'product_id'       => $product->product_id,
                'product_name'     => $product->product_name,
                'category_name'    => $category ?? '',
                'meta_keywords'    => '',
                'meta_description' => '',
            ];
        });
    }

    public function headings(): array
    {
        return ['product_id', 'product_name', 'category_name', 'meta_keywords', 'meta_description'];
    }
}
