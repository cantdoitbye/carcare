<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        protected Product $model
    ) {}

    public function all()
    {
        return $this->model->with('categories', 'images')->latest()->get();
    }

    public function find($id)
    {
        return $this->model->with('categories', 'images')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function getActive()
    {
        return $this->model->active()->with('categories', 'images')->get();
    }

    public function getFeatured()
    {
        return $this->model->active()->featured()->with('categories', 'images')->get();
    }

    public function getByCategory($categoryId)
    {
        return $this->model->active()
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->with('categories', 'images')
            ->get();
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->with('categories', 'images')->firstOrFail();
    }

    public function findBySku($sku)
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function getLowStock()
    {
        return $this->model->whereColumn('quantity', '<=', 'min_quantity')->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->orWhere('brand', 'LIKE', "%{$query}%")
            ->with('categories', 'images')
            ->get();
    }

    public function updateStock($id, $quantity)
    {
        $product = $this->find($id);
        $product->quantity = $quantity;
        $product->updateStockStatus();
        return $product;
    }

    public function bulkUpdateStatus(array $ids, $status)
    {
        return $this->model->whereIn('id', $ids)->update(['status' => $status]);
    }

    public function getWithFilters(array $filters)
    {
        $query = $this->model->with('categories', 'images');

        if (isset($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (isset($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['stock_status'])) {
            $query->where('stock_status', $filters['stock_status']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }
}