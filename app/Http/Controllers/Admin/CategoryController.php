<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index()
    {
        $categories = $this->categoryRepository->getTreeStructure();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = $this->categoryRepository->getParentCategories();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $this->categoryRepository->create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        $category = $this->categoryRepository->find($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);
        $parentCategories = $this->categoryRepository->getParentCategories();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $this->categoryRepository->update($id, $data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $this->categoryRepository->delete($id);
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:categories,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        $this->categoryRepository->updateSortOrder($request->orders);

        return response()->json(['success' => true]);
    }
}
