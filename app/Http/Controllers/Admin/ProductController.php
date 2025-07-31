<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['category_id', 'brand', 'status', 'stock_status', 'search']);
        $products = $this->productRepository->getWithFilters($filters);
        $categories = $this->categoryRepository->getActive();
        
        // Get unique brands for filter
        $brands = $this->productRepository->all()->pluck('brand')->filter()->unique()->sort();

        return view('admin.products.index', compact('products', 'categories', 'brands', 'filters'));
    }

    public function create()
    {
        $categories = $this->categoryRepository->getActive();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products,sku',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'track_inventory' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'attributes' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        // Process tags
        if ($request->tags) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // Create product
        $product = $this->productRepository->create($data);

        // Attach categories
        $product->categories()->attach($request->categories);

        // Handle images
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        // Update stock status
        $product->updateStockStatus();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = $this->productRepository->find($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productRepository->find($id);
        $categories = $this->categoryRepository->getActive();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products,sku,' . $id,
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'track_inventory' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'attributes' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        // Process tags
        if ($request->tags) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // Update product
        $product = $this->productRepository->update($id, $data);

        // Update categories
        $product->categories()->sync($request->categories);

        // Handle new images
        if ($request->hasFile('new_images')) {
            $this->handleImageUploads($product, $request->file('new_images'));
        }

        // Update stock status
        $product->updateStockStatus();

        return redirect()->route('admin.products.edit', $id)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = $this->productRepository->find($id);
        
        // Delete associated images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $this->productRepository->delete($id);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,draft',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:products,id',
        ]);

        $ids = $request->selected_ids;

        switch ($request->action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->destroy($id);
                }
                $message = 'Selected products deleted successfully.';
                break;

            case 'activate':
                $this->productRepository->bulkUpdateStatus($ids, 'active');
                $message = 'Selected products activated successfully.';
                break;

            case 'deactivate':
                $this->productRepository->bulkUpdateStatus($ids, 'inactive');
                $message = 'Selected products deactivated successfully.';
                break;

            case 'draft':
                $this->productRepository->bulkUpdateStatus($ids, 'draft');
                $message = 'Selected products moved to draft successfully.';
                break;
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    public function deleteImage($productId, $imageId)
    {
        $product = $this->productRepository->find($productId);
        $image = $product->images()->findOrFail($imageId);
        
        // Delete file from storage
        Storage::disk('public')->delete($image->image_path);
        
        // Delete database record
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage($productId, $imageId)
    {
        $product = $this->productRepository->find($productId);
        
        // Remove primary flag from all images
        $product->images()->update(['is_primary' => false]);
        
        // Set new primary image
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        // This will be implemented with CSV export functionality
        $filters = $request->only(['category_id', 'brand', 'status', 'stock_status', 'search']);
        $products = $this->productRepository->getWithFilters($filters);

        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'SKU', 'Brand', 'Price', 'Compare Price', 'Quantity', 
                'Weight', 'Status', 'Categories', 'Created At'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->brand,
                    $product->price,
                    $product->compare_price,
                    $product->quantity,
                    $product->weight,
                    $product->status,
                    $product->categories->pluck('name')->implode(', '),
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function lowStock()
    {
        $products = $this->productRepository->getLowStock();
        return view('admin.products.low-stock', compact('products'));
    }

    protected function handleImageUploads($product, $images)
    {
        $isFirstImage = $product->images()->count() === 0;
        
        foreach ($images as $index => $image) {
            $imagePath = $image->store('products', 'public');
            
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'alt_text' => $product->name . ' - Image ' . ($index + 1),
                'sort_order' => $index,
                'is_primary' => $isFirstImage && $index === 0,
            ]);
        }
    }
}
