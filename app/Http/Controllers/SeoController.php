<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeoExport;
use App\Imports\SeoImport;

class SeoController extends Controller
{
    
    public function seo()
    {
     return view('product.seo');    
    }

    public function export()
    {
        return Excel::download(new SeoExport, 'seo_products.xlsx');
    }

    // Import and update SEO fields
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new SeoImport, $request->file('file'));

        return back()->with('success', 'SEO data imported successfully!');
    }
}
