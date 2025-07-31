@extends('admin.layouts.app')

@section('title', 'Categories')
@section('page-title', 'Category Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="categoriesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->icon)
                                            <i class="{{ $category->icon }} me-2 text-primary"></i>
                                        @endif
                                        <div>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $category->parent ? $category->parent->name : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->products->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm sort-order" 
                                           value="{{ $category->sort_order }}" style="width: 80px;" data-id="{{ $category->id }}">
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteCategory({{ $category->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Child categories -->
                            @foreach($category->children as $child)
                                <tr data-id="{{ $child->id }}" class="table-light">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-3">└─</span>
                                            @if($child->icon)
                                                <i class="{{ $child->icon }} me-2 text-primary"></i>
                                            @endif
                                            <div>
                                                <strong>{{ $child->name }}</strong>
                                                @if($child->description)
                                                    <br><small class="text-muted">{{ Str::limit($child->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $child->parent->name }}</td>
                                    <td><span class="badge bg-info">{{ $child->products->count() }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $child->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($child->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm sort-order" 
                                               value="{{ $child->sort_order }}" style="width: 80px;" data-id="{{ $child->id }}">
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCategory({{ $child->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5>No categories found</h5>
                <p class="text-muted">Start by creating your first category</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Category
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteCategory(id) {
    document.getElementById('deleteForm').action = '/admin/categories/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-save sort order changes
document.querySelectorAll('.sort-order').forEach(function(input) {
    input.addEventListener('change', function() {
        const id = this.dataset.id;
        const sortOrder = this.value;
        
        fetch('/admin/categories/sort-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                orders: [{id: id, sort_order: sortOrder}]
            })
        });
    });
});
</script>
@endpush