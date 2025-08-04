<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:100',
            'type' => 'required|in:hero,promotional,sidebar',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $data = $request->all();
        
        // Handle image upload to public folder
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Create banners directory if it doesn't exist
            $uploadPath = public_path('uploads/banners');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            
            // Move image to public folder
            $image->move($uploadPath, $imageName);
            $data['image_path'] = 'uploads/banners/' . $imageName;
        }

        // Set default values
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $request->sort_order ?? 0;

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Delete image file from public folder
        if ($banner->image_path && File::exists(public_path($banner->image_path))) {
            File::delete(public_path($banner->image_path));
        }
        
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }
}