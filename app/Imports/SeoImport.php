<?php 

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SeoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Update product SEO fields based on product_id
        // $product = Product::find($row['product_id']);

        $product = Product::where('product_id', $row['product_id'] ?? null)
            ->where('status', 1)
            ->first();

        if ($product) {
            $product->meta_keywords = $row['meta_keywords'] ?? '';
            $product->meta_description = $row['meta_description'] ?? '';
            $product->seo = 1; // mark as filled
            $product->save();
        }

        return null;
    }
}

?>