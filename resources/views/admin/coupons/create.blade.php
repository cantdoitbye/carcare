{{-- resources/views/admin/coupons/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Create Coupon')
@section('page-title', 'Create New Coupon')

@section('content')
<form action="{{ route('admin.coupons.store') }}" method="POST" id="couponForm">
    @csrf
    
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
                                <label class="form-label">Coupon Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Coupon Code *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" value="{{ old('code') }}" id="couponCode" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Discount Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Discount Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Discount Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" id="discountType" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Discount Value *</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">%</span>
                                    <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                           name="value" value="{{ old('value') }}" step="0.01" min="0" required>
                                </div>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Minimum Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                           name="minimum_amount" value="{{ old('minimum_amount') }}" step="0.01" min="0">
                                </div>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row" id="maxDiscountRow" style="display: none;">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Maximum Discount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                           name="maximum_discount" value="{{ old('maximum_discount') }}" step="0.01" min="0">
                                </div>
                                <small class="text-muted">Only for percentage discounts</small>
                                @error('maximum_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="free_shipping" 
                                       value="1" {{ old('free_shipping') ? 'checked' : '' }}>
                                <label class="form-check-label">Free Shipping</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="exclude_sale_items" 
                                       value="1" {{ old('exclude_sale_items') ? 'checked' : '' }}>
                                <label class="form-check-label">Exclude Sale Items</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Restrictions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usage Restrictions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usage Limit</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       name="usage_limit" value="{{ old('usage_limit') }}" min="1">
                                <small class="text-muted">Leave empty for unlimited uses</small>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usage Limit per Customer</label>
                                <input type="number" class="form-control @error('usage_limit_per_customer') is-invalid @enderror" 
                                       name="usage_limit_per_customer" value="{{ old('usage_limit_per_customer') }}" min="1">
                                <small class="text-muted">Leave empty for unlimited per customer</small>
                                @error('usage_limit_per_customer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                       name="starts_at" value="{{ old('starts_at') }}">
                                <small class="text-muted">Leave empty to start immediately</small>
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       name="expires_at" value="{{ old('expires_at') }}">
                                <small class="text-muted">Leave empty for no expiry</small>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product/Category Restrictions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product & Category Restrictions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Applicable Products</label>
                                <select class="form-select" name="applicable_products[]" multiple size="6">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ in_array($product->id, old('applicable_products', [])) ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave empty to apply to all products</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Applicable Categories</label>
                                <select class="form-select" name="applicable_categories[]" multiple size="6">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('applicable_categories', [])) ? 'selected' : '' }}>
                                            {{ $category->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave empty to apply to all categories</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Excluded Products</label>
                                <select class="form-select" name="excluded_products[]" multiple size="4">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ in_array($product->id, old('excluded_products', [])) ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Excluded Categories</label>
                                <select class="form-select" name="excluded_categories[]" multiple size="4">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('excluded_categories', [])) ? 'selected' : '' }}>
                                            {{ $category->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_public" 
                               value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                        <label class="form-check-label">Public Coupon</label>
                        <small class="d-block text-muted">Uncheck for private/exclusive coupons</small>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Coupon Preview</h5>
                </div>
                <div class="card-body">
                    <div class="coupon-preview border rounded p-3 text-center bg-light">
                        <h4 class="text-primary mb-1" id="previewCode">COUPON CODE</h4>
                        <h5 class="mb-2" id="previewName">Coupon Name</h5>
                        <div class="badge bg-primary fs-6 mb-2" id="previewValue">0% OFF</div>
                        <p class="text-muted small mb-0" id="previewDescription">Coupon description</p>
                        <div class="mt-2">
                            <small class="text-muted" id="previewExpiry">No expiry date</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Coupon
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
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
// Generate coupon code
function generateCode() {
    fetch('/admin/generate-coupon-code')
        .then(response => response.json())
        .then(data => {
            document.getElementById('couponCode').value = data.code;
            updatePreview();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Update discount type UI
document.getElementById('discountType').addEventListener('change', function() {
    const type = this.value;
    const prefix = document.getElementById('valuePrefix');
    const maxDiscountRow = document.getElementById('maxDiscountRow');
    
    if (type === 'percentage') {
        prefix.textContent = '%';
        maxDiscountRow.style.display = 'block';
    } else {
        prefix.textContent = '₹';
        maxDiscountRow.style.display = 'none';
    }
    updatePreview();
});

// Update preview
function updatePreview() {
    const code = document.querySelector('input[name="code"]').value || 'COUPON CODE';
    const name = document.querySelector('input[name="name"]').value || 'Coupon Name';
    const type = document.querySelector('select[name="type"]').value;
    const value = document.querySelector('input[name="value"]').value || '0';
    const description = document.querySelector('textarea[name="description"]').value || 'Coupon description';
    const expiresAt = document.querySelector('input[name="expires_at"]').value;
    
    document.getElementById('previewCode').textContent = code;
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewDescription').textContent = description;
    
    if (type === 'percentage') {
        document.getElementById('previewValue').textContent = value + '% OFF';
    } else {
        document.getElementById('previewValue').textContent = '$' + value + ' OFF';
    }
    
    if (expiresAt) {
        const expiry = new Date(expiresAt);
        document.getElementById('previewExpiry').textContent = 'Expires: ' + expiry.toLocaleDateString();
    } else {
        document.getElementById('previewExpiry').textContent = 'No expiry date';
    }
}

// Add event listeners for real-time preview
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners
    document.querySelector('input[name="code"]').addEventListener('input', updatePreview);
    document.querySelector('input[name="name"]').addEventListener('input', updatePreview);
    document.querySelector('input[name="value"]').addEventListener('input', updatePreview);
    document.querySelector('textarea[name="description"]').addEventListener('input', updatePreview);
    document.querySelector('input[name="expires_at"]').addEventListener('input', updatePreview);
    document.querySelector('select[name="type"]').addEventListener('change', updatePreview);
    
    // Initialize preview
    updatePreview();
    
    // Initialize discount type
    document.getElementById('discountType').dispatchEvent(new Event('change'));
});
</script>
@endpush