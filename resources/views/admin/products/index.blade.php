@extends('admin.layouts.app')

@section('title', 'Products')
@section('page-title', 'Product Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <div class="btn-group">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.products.import') }}">
                <i class="fas fa-upload me-2"></i>Import Products</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.products.export') }}">
                <i class="fas fa-download me-2"></i>Export Products</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.products.low-stock') }}">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</a></li>
        </ul>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="brand">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <form id="bulkActionForm" method="POST" action="{{ route('admin.products.bulk-action') }}">
            @csrf
            <div class="row align-items-center">
                <div class="col-auto">
                    <span id="selectedCount">0</span> products selected
                </div>
                <div class="col-auto">
                    <select class="form-select" name="action" required>
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="draft">Move to Draft</option>
                        <option value="delete">Delete</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSelection()">Clear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" 
                                           value="{{ $product->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" 
                                                 class="rounded me-3" width="50" height="50" 
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->is_featured)
                                                <span class="badge bg-warning ms-1">Featured</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                {{ $product->categories->pluck('name')->implode(', ') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $product->sku }}</code>
                                </td>
                                <td>{{ $product->brand ?: '-' }}</td>
                                <td>
                                    <div>
                                        {{ number_format($product->price, 2) }}
                                        @if($product->has_discount)
                                            <br><small class="text-muted">
                                                <s>{{ number_format($product->compare_price, 2) }}</s>
                                                <span class="text-danger">-{{ $product->discount_percentage }}%</span>
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $product->quantity }}
                                        <br>
                                        <span class="badge bg-{{ $product->stock_status === 'in_stock' ? 'success' : ($product->stock_status === 'low_stock' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'inactive' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteProduct({{ $product->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p class="text-muted">Start by adding your first product</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Product
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
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
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
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox handling
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActionsCard = document.getElementById('bulkActionsCard');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedCheckboxes.length > 0) {
        bulkActionsCard.style.display = 'block';
        selectedCount.textContent = selectedCheckboxes.length;
        
        // Update hidden inputs for bulk action
        const bulkForm = document.getElementById('bulkActionForm');
        const existingInputs = bulkForm.querySelectorAll('input[name="selected_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = checkbox.value;
            bulkForm.appendChild(input);
        });
    } else {
        bulkActionsCard.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function deleteProduct(id) {
    document.getElementById('deleteForm').action = '/admin/products/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Bulk action form submission
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const action = this.querySelector('select[name="action"]').value;
    if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected products? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush