@extends('admin.layouts.app')

@section('title', $product->name)
@section('page-title', 'Product Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $product->name }}</h1>
    <div class="btn-group">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Product
        </a>
        <button type="button" class="btn btn-outline-danger" onclick="deleteProduct()">
            <i class="fas fa-trash me-2"></i>Delete
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Product Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Name:</td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SKU:</td>
                                <td><code>{{ $product->sku }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Brand:</td>
                                <td>{{ $product->brand ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'inactive' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Featured:</td>
                                <td>
                                    @if($product->is_featured)
                                        <span class="badge bg-warning"><i class="fas fa-star me-1"></i>Featured</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Price:</td>
                                <td>
                                    <strong>${{ number_format($product->price, 2) }}</strong>
                                    @if($product->compare_price)
                                        <br><small class="text-muted">
                                            Compare: <s>${{ number_format($product->compare_price, 2) }}</s>
                                        </small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cost Price:</td>
                                <td>{{ $product->cost_price ? '$' . number_format($product->cost_price, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Discount:</td>
                                <td>{{ $product->discount_percentage > 0 ? $product->discount_percentage . '%' : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Weight:</td>
                                <td>{{ $product->weight ? $product->weight . ' kg' : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dimensions:</td>
                                <td>
                                    @if($product->length || $product->width || $product->height)
                                        {{ $product->length ?? '0' }} × {{ $product->width ?? '0' }} × {{ $product->height ?? '0' }} cm
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descriptions -->
        @if($product->short_description || $product->description)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Descriptions</h5>
                </div>
                <div class="card-body">
                    @if($product->short_description)
                        <div class="mb-3">
                            <h6>Short Description</h6>
                            <p class="text-muted">{{ $product->short_description }}</p>
                        </div>
                    @endif
                    
                    @if($product->description)
                        <div>
                            <h6>Full Description</h6>
                            <div>{!! nl2br(e($product->description)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- SEO Information -->
        @if($product->meta_title || $product->meta_description)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">SEO Information</h5>
                </div>
                <div class="card-body">
                    @if($product->meta_title)
                        <div class="mb-3">
                            <h6>Meta Title</h6>
                            <p class="text-muted">{{ $product->meta_title }}</p>
                        </div>
                    @endif
                    
                    @if($product->meta_description)
                        <div class="mb-3">
                            <h6>Meta Description</h6>
                            <p class="text-muted">{{ $product->meta_description }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <h6>Slug</h6>
                        <code>{{ $product->slug }}</code>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Inventory Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Inventory Status</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-4 fw-bold">{{ $product->quantity }}</div>
                    <div class="text-muted">Units in Stock</div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Stock Status:</span>
                    <span class="badge bg-{{ $product->stock_status === 'in_stock' ? 'success' : ($product->stock_status === 'low_stock' ? 'warning' : 'danger') }}">
                        {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Min. Quantity:</span>
                    <span>{{ $product->min_quantity }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Track Inventory:</span>
                    <span>{{ $product->track_inventory ? 'Yes' : 'No' }}</span>
                </div>
                
                @if($product->isLowStock())
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Low Stock Alert!</strong> This product is running low.
                    </div>
                @endif
            </div>
        </div>

        <!-- Categories -->
        @if($product->categories->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    @foreach($product->categories as $category)
                        <span class="badge bg-primary me-1 mb-1">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tags -->
        @if($product->tags && count($product->tags) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    @foreach($product->tags as $tag)
                        <span class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Product Images -->
        @if($product->images->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Images</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($product->images as $image)
                            <div class="col-6">
                                <div class="card position-relative">
                                    <img src="{{ $image->image_url }}" class="card-img-top" 
                                         style="height: 120px; object-fit: cover; cursor: pointer;" 
                                         onclick="showImageModal('{{ $image->image_url }}', '{{ $image->alt_text }}')">
                                    @if($image->is_primary)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i> Primary
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

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Product
                    </a>
                    
                    @if($product->status === 'active')
                        <form action="{{ route('admin.products.bulk-action') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="action" value="deactivate">
                            <input type="hidden" name="selected_ids[]" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-eye-slash me-2"></i>Deactivate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.products.bulk-action') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="action" value="activate">
                            <input type="hidden" name="selected_ids[]" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-eye me-2"></i>Activate
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
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
                Are you sure you want to delete "{{ $product->name }}"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showImageModal(imageUrl, altText) {
    document.getElementById('modalImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function deleteProduct() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush