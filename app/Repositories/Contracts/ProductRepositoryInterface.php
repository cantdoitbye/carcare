<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function getFeatured();
    public function getByCategory($categoryId);
    public function findBySlug($slug);
    public function findBySku($sku);
    public function getLowStock();
    public function search($query);
    public function updateStock($id, $quantity);
    public function bulkUpdateStatus(array $ids, $status);
    public function getWithFilters(array $filters);
}