<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'hero'); // hero, promotional, sidebar
        
        $banners = Banner::byType($type)
                        ->ordered()
                        ->get()
                        ->map(function ($banner) {
                            return [
                                'id' => $banner->id,
                                'title' => $banner->title,
                                'description' => $banner->description,
                                'image_url' => $banner->image_url,
                                'link_url' => $banner->link_url,
                                'link_text' => $banner->link_text,
                                'type' => $banner->type,
                            ];
                        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'banners' => $banners
            ]
        ]);
    }
}