<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Domain;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class BannerController extends Controller
{
    public function index()
    {
        $bannerFiles = Banner::with('domain')->orderBy('created_at', 'desc')->paginate(10);
        $domains = Domain::all();
        return view('banner.index', compact('bannerFiles', 'domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_name'  => 'required|string|max:255',
            'domains'      => 'required|array',
            'domains.*'    => 'exists:domains,domain_id',
            'banner_file'  => 'required|file|mimes:jpg,jpeg,png,pdf,docx,mp4|max:20480',
        ]);

        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            $filePath  = 'uploads/' . $filename;
            $extension = strtolower($file->getClientOriginalExtension());

            Banner::create([
                'banner_name' => $request->banner_name,
                'domains'     => implode(',', $request->domains),
                'file_path'   => $filePath,
                'file_type'   => $extension,
                'created_by'  => session('user_id'),
            ]);

            return redirect()->route('bannerIndex')->with('success', 'Banner uploaded successfully.');
        }

        return redirect()->route('bannerIndex')->with('error', 'No file selected.');
    }


    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        $filePath = public_path($banner->file_path);

        try {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            return redirect()->route('bannerIndex')->with('error', 'File deletion failed: ' . $e->getMessage());
        }

        $banner->delete();

        return redirect()->route('bannerIndex')->with('success', 'Banner deleted successfully.');
    }
  
   
}
