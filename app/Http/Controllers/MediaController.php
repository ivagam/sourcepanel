<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class MediaController extends Controller
{
    public function addMedia()
    {
        $categories = Category::all();
        return view('media.addMedia', compact('categories'));
    }

    public function mediaList()
    {
        $mediaFiles = Media::with('category')->orderBy('created_at', 'desc')->paginate(10);
        return view('media.mediaList', compact('mediaFiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:category,category_id',
            'media_file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,mp4|max:20480',
        ]);

         $categoryId = $request->category_id;
            $categoryIds = [];

            while ($categoryId) {
                $category = \App\Models\Category::find($categoryId);
                if (!$category) break;

                array_unshift($categoryIds, $category->category_id); // prepend
                $categoryId = $category->subcategory_id;
            }

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            $filePath = 'uploads/' . $filename;
            $extension = strtolower($file->getClientOriginalExtension());

        Media::create([
            'category_id' => $request->category_id,
            'file_path' => $filePath,
            'file_type' => $extension,
            'created_by' => session('user_id'),
            'category_ids' => implode(',', $categoryIds),
        ]);

        return redirect()->route('mediaList')->with('success', 'Media uploaded successfully.');
    }
        return redirect()->route('mediaList')->with('error', 'No file selected.');
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        $filePath = public_path($media->file_path);

        try {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            return redirect()->route('mediaList')->with('error', 'File deletion failed: ' . $e->getMessage());
        }

        $media->delete();

        return redirect()->route('mediaList')->with('success', 'Media deleted successfully.');
    }
  
   
}
