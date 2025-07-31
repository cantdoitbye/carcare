<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductImportController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function showImportForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $csvData = array_map('str_getcsv', file($path));
        $header = array_shift($csvData);
        
        $results = [
            'imported' => 0,
            'updated' => 0,
            'errors' => [],
        ];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because we start from row 2 (after header)
            
            if (count($header) !== count($row)) {
                $results['errors'][] = "Row {$rowNumber}: Column count mismatch";
                continue;
            }

            $data = array_combine($header, $row);
            
            try {
                $this->processProductRow($data, $results, $rowNumber);
            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        return redirect()->route('admin.products.import')
            ->with('import_results', $results);
    }

    public function downloadTemplate()
    {
        $filename = 'product_import_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'name', 'sku', 'brand', 'price', 'compare_price', 'cost_price', 
                'quantity', 'min_quantity', 'weight', 'status', 'is_featured', 
                'description', 'short_description', 'categories', 'tags'
            ]);

            // Sample data
            fputcsv($file, [
                'Sample Product', 'SKU-001', 'Brand Name', '29.99', '39.99', '15.00',
                '100', '10', '0.5', 'active', '0', 
                'Product description here', 'Short description', 'car-wash,interior-care', 'tag1,tag2'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function processProductRow($data, &$results, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Check if product exists
        $existingProduct = Product::where('sku', $data['sku'])->first();
        
        // Prepare product data
        $productData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'sku' => $data['sku'],
            'brand' => $data['brand'] ?? null,
            'price' => (float) $data['price'],
            'compare_price' => !empty($data['compare_price']) ? (float) $data['compare_price'] : null,
            'cost_price' => !empty($data['cost_price']) ? (float) $data['cost_price'] : null,
            'quantity' => (int) $data['quantity'],
            'min_quantity' => !empty($data['min_quantity']) ? (int) $data['min_quantity'] : 0,
            'weight' => !empty($data['weight']) ? (float) $data['weight'] : null,
            'status' => in_array($data['status'] ?? '', ['active', 'inactive', 'draft']) ? $data['status'] : 'draft',
            'is_featured' => in_array($data['is_featured'] ?? '', ['1', 'true', 'yes']) ? true : false,
            'description' => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
        ];

        // Process tags
        if (!empty($data['tags'])) {
            $productData['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        if ($existingProduct) {
            // Update existing product
            $existingProduct->update($productData);
            $product = $existingProduct;
            $results['updated']++;
        } else {
            // Create new product
            $product = Product::create($productData);
            $results['imported']++;
        }

        // Handle categories
        if (!empty($data['categories'])) {
            $categorySlugIds = [];
            $categorySlugs = array_map('trim', explode(',', $data['categories']));
            
            foreach ($categorySlugs as $slug) {
                $category = Category::where('slug', $slug)->first();
                if ($category) {
                    $categorySlugIds[] = $category->id;
                }
            }
            
            if (!empty($categorySlugIds)) {
                $product->categories()->sync($categorySlugIds);
            }
        }

        // Update stock status
        $product->updateStockStatus();
    }
}
