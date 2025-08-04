@extends('admin.layouts.app')

@section('title', 'Banner Management')
@section('page-title', 'Banner Management')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header with Add Button -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Banners</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                    <i class="fas fa-plus"></i> Add New Banner
                </button>
            </div>
            
            <div class="card-body">
                @if($banners->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Sort Order</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                            <tr>
                                <td>
                                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" 
                                         class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->description)
                                        <br><small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                    @endif
                                    @if($banner->link_url)
                                        <br><small class="text-info">
                                            <i class="fas fa-link"></i> {{ $banner->link_text ?: 'Link' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $banner->type === 'hero' ? 'primary' : ($banner->type === 'promotional' ? 'success' : 'info') }}">
                                        {{ ucfirst($banner->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($banner->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $banner->sort_order }}</td>
                                <td>{{ $banner->created_at->format('M d, Y') }}</td>
                                <td>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Banners Found</h5>
                    <p class="text-muted">Click "Add New Banner" to create your first banner.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBannerModalLabel">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Banner Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="hero" {{ old('type') === 'hero' ? 'selected' : '' }}>Hero Banner</option>
                                    <option value="promotional" {{ old('type') === 'promotional' ? 'selected' : '' }}>Promotional</option>
                                    <option value="sidebar" {{ old('type') === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3" placeholder="Optional banner description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Banner Image *</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" required>
                        <small class="form-text text-muted">Upload JPG, PNG, GIF (Max: 2MB)</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Link URL</label>
                                <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                                       name="link_url" value="{{ old('link_url') }}" placeholder="https://example.com">
                                @error('link_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Link Text</label>
                                <input type="text" class="form-control @error('link_text') is-invalid @enderror" 
                                       name="link_text" value="{{ old('link_text') }}" placeholder="Learn More">
                                @error('link_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Starts At</label>
                                <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                       name="starts_at" value="{{ old('starts_at') }}">
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Expires At</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Banner
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Close modal and reset form on successful submission
    @if(session('success'))
        // Hide modal if it was open
        var modal = bootstrap.Modal.getInstance(document.getElementById('addBannerModal'));
        if (modal) {
            modal.hide();
        }
    @endif

    // Reset form when modal is closed
    document.getElementById('addBannerModal').addEventListener('hidden.bs.modal', function () {
        this.querySelector('form').reset();
        // Remove validation classes
        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    });

    // Preview image before upload (optional enhancement)
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // You can add image preview functionality here if needed
            console.log('Selected file:', file.name);
        }
    });
</script>
@endpush