@extends('admin.layouts.app')

@section('title', 'Dashboard - Car Care Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-value">{{ number_format($totalProducts) }}</div>
        <div class="stat-label">Total Products</div>
        <div class="stat-change">
            <small class="text-success">{{ $activeProducts }} active</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
            <i class="fas fa-folder"></i>
        </div>
        <div class="stat-value">{{ number_format($totalCategories) }}</div>
        <div class="stat-label">Categories</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-value">{{ number_format($lowStockProducts) }}</div>
        <div class="stat-label">Low Stock Items</div>
        @if($lowStockProducts > 0)
            <div class="stat-change">
                <small class="text-warning">Needs attention</small>
            </div>
        @endif
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-label">Orders Today</div>
        <div class="stat-change">
            <small class="text-muted">Coming soon</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Products</h5>
            </div>
            <div class="card-body">
                @if($recentProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <br><small class="text-muted">{{ $product->categories->pluck('name')->implode(', ') }}</small>
                                            </div>
                                        </td>
                                        <td><code>{{ $product->sku }}</code></td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>
                                            <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No products yet</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                            Add Your First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-folder-plus me-2"></i>Add Category
                    </a>
                    <a href="{{ route('admin.products.import') }}" class="btn btn-outline-primary">
                        <i class="fas fa-upload me-2"></i>Import Products
                    </a>
                    @if($lowStockProducts > 0)
                        <a href="{{ route('admin.products.low-stock') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Check Low Stock ({{ $lowStockProducts }})
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if($lowStockItems->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($lowStockItems as $item)
                        <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-2 pb-2 border-bottom' : '' }}">
                            <div>
                                <strong class="d-block">{{ $item->name }}</strong>
                                <small class="text-muted">{{ $item->sku }}</small>
                            </div>
                            <span class="badge bg-warning">{{ $item->quantity }}</span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.products.low-stock') }}" class="btn btn-sm btn-warning">
                            View All Low Stock Items
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-change {
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Add smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stat cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Add click effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function() {
                // Add ripple effect
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(37, 99, 235, 0.3)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.width = '100px';
                ripple.style.height = '100px';
                ripple.style.left = '50%';
                ripple.style.top = '50%';
                ripple.style.marginLeft = '-50px';
                ripple.style.marginTop = '-50px';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
</script>
@endpush