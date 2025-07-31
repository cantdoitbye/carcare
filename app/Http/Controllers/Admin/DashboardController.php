<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
    {
        // Sample data - replace with actual repository calls
        $data = [
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('status', 'active')->count(),
            'totalCategories' => Category::count(),
            'lowStockProducts' => Product::whereColumn('quantity', '<=', 'min_quantity')->count(),
            'totalRevenue' => 0, // Will be calculated from orders later
            'recentProducts' => Product::with('categories')
                ->latest()
                ->take(5)
                ->get(),
            'lowStockItems' => Product::whereColumn('quantity', '<=', 'min_quantity')
                ->with('categories')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
