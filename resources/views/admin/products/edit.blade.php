@extends('admin.layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       name="sku" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                  name="short_description" rows="2" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="6">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       name="brand" value="{{ old('brand', $product->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                       name="tags" value="{{ old('tags', $product->tags ? implode(', ', $product->tags) : '') }}" 
                                       placeholder="Enter tags separated by commas">
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pricing</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Compare Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('compare_price') is-invalid @enderror" 
                                           name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" step="0.01" min="0">
                                </div>
                                @error('compare_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                           name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0">
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Discount Percentage</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           name="discount_percentage" value="{{ old('discount_percentage', $product->discount_percentage) }}" 
                                           step="0.01" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inventory Management</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       name="quantity" value="{{ old('quantity', $product->quantity) }}" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Minimum Quantity</label>
                                <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" 
                                       name="min_quantity" value="{{ old('min_quantity', $product->min_quantity) }}" min="0">
                                @error('min_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="track_inventory" 
                               value="1" {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}>
                        <label class="form-check-label">Track inventory for this product</label>
                    </div>
                </div>
            </div>

            <!-- Physical Properties -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Physical Properties</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Weight</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                           name="weight" value="{{ old('weight', $product->weight) }}" step="0.001" min="0">
                                    <span class="input-group-text">kg</span>
                                </div>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Length</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('length') is-invalid @enderror" 
                                           name="length" value="{{ old('length', $product->length) }}" step="0.01" min="0">
                                    <span class="input-group-text">cm</span>
                                </div>
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Width</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('width') is-invalid @enderror" 
                                           name="width" value="{{ old('width', $product->width) }}" step="0.01" min="0">
                                    <span class="input-group-text">cm</span>
                                </div>
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Height</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('height') is-invalid @enderror" 
                                           name="height" value="{{ old('height', $product->height) }}" step="0.01" min="0">
                                    <span class="input-group-text">cm</span>
                                </div>
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">SEO Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                               name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="255">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                  name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $product->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               name="slug" value="{{ old('slug', $product->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status & Visibility -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status & Visibility</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_featured" 
                               value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Product</label>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_digital" 
                               value="1" {{ old('is_digital', $product->is_digital) ? 'checked' : '' }}>
                        <label class="form-check-label">Digital Product</label>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categories *</h5>
                </div>
                <div class="card-body">
                    @error('categories')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    
                    <div class="category-tree">
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" 
                                       name="categories[]" value="{{ $category->id }}"
                                       {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ $category->name }}
                                </label>
                            </div>
                            @if($category->children->count() > 0)
                                <div class="ms-4">
                                    @foreach($category->children as $child)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" 
                                                   name="categories[]" value="{{ $child->id }}"
                                                   {{ in_array($child->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ $child->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Existing Images -->
            @if($product->images->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2" id="existingImages">
                            @foreach($product->images as $image)
                                <div class="col-6" data-image-id="{{ $image->id }}">
                                    <div class="card position-relative">
                                        <img src="{{ $image->image_url }}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $image->is_primary ? 'Primary' : 'Gallery' }}
                                                </small>
                                                <div class="btn-group btn-group-sm">
                                                    @if(!$image->is_primary)
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                onclick="setPrimary({{ $image->id }})" title="Set as Primary">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="deleteImage({{ $image->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @if($image->is_primary)
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add New Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Upload New Images</label>
                        <input type="file" class="form-control @error('new_images') is-invalid @enderror" 
                               name="new_images[]" multiple accept="image/*" id="imageInput">
                        @error('new_images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="imagePreview" class="row g-2"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>View Product
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Image preview functionality
document.getElementById('imageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6';
                
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">New Image</small>
                        </div>
                    </div>
                `;
                
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Delete image function
function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/products/{{ $product->id }}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-image-id="${imageId}"]`).remove();
            }
        });
    }
}

// Set primary image function
function setPrimary(imageId) {
    fetch(`/admin/products/{{ $product->id }}/images/${imageId}/primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush