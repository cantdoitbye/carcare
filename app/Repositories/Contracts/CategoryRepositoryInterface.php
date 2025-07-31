<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function getParentCategories();
    public function getChildCategories($parentId);
    public function findBySlug($slug);
    public function getTreeStructure();
    public function updateSortOrder(array $orders);
}