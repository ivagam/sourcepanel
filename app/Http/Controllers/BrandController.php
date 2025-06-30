<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Domain;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class BrandController extends Controller
{
    public function index()
    {
        $brandFiles = Brand::with('domain')->orderBy('created_at', 'desc')->get();
        $domains = Domain::all();
        return view('brand.index', compact('brandFiles', 'domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_name'  => 'required|string|max:255',
            'domains'      => 'required|array',
            'domains.*'    => 'exists:domains,domain_id',
            'brand_file'  => 'required|file|mimes:jpg,jpeg,png,pdf,docx,mp4|max:20480',
        ]);

        if ($request->hasFile('brand_file')) {
            $file = $request->file('brand_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            $filePath  = 'uploads/' . $filename;
            $extension = strtolower($file->getClientOriginalExtension());

            Brand::create([
                'brand_name' => $request->brand_name,
                'domains'     => implode(',', $request->domains),
                'file_path'   => $filePath,
                'file_type'   => $extension,
                'created_by'  => session('user_id'),
            ]);

            return redirect()->route('brandIndex')->with('success', 'Brand uploaded successfully.');
        }

        return redirect()->route('brandIndex')->with('error', 'No file selected.');
    }


    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        $filePath = public_path($brand->file_path);

        try {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            return redirect()->route('brandIndex')->with('error', 'File deletion failed: ' . $e->getMessage());
        }

        $brand->delete();

        return redirect()->route('brandIndex')->with('success', 'Brand deleted successfully.');
    }
  
   
}
