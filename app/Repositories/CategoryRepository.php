<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        protected Category $model
    ) {}

    public function all()
    {
        return $this->model->with('parent', 'children')->orderBy('sort_order')->get();
    }

    public function find($id)
    {
        return $this->model->with('parent', 'children')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        
        // Move children to parent level
        if ($category->hasChildren()) {
            $category->children()->update(['parent_id' => $category->parent_id]);
        }
        
        return $category->delete();
    }

    public function getActive()
    {
        return $this->model->active()->orderBy('sort_order')->get();
    }

    public function getParentCategories()
    {
        return $this->model->whereNull('parent_id')->active()->orderBy('sort_order')->get();
    }

    public function getChildCategories($parentId)
    {
        return $this->model->where('parent_id', $parentId)->active()->orderBy('sort_order')->get();
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getTreeStructure()
    {
        return $this->model->with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
    }

    public function updateSortOrder(array $orders)
    {
        foreach ($orders as $order) {
            $this->model->where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
        }
    }
}