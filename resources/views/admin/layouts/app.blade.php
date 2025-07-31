<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Car Care Admin Panel')</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --car-blue: #1e40af;
            --car-orange: #ea580c;
            --car-gray: #374151;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .sidebar {
            background: white;
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-left: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: block;
            padding: 15px 20px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            margin-left: 280px;
            padding: 0;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-area {
            padding: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 15px 15px 0 0 !important;
        }

        .card-body {
            padding: 20px;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark-color);
        }
        .menu-section {
    margin-bottom: 1rem;
}

.menu-section-title {
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    color: var(--text-muted);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.menu-item:hover, .menu-item.active {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary-color);
    border-left-color: var(--primary-color);
    transform: translateX(2px);
}

.menu-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.menu-item .badge {
    font-size: 0.6rem;
    padding: 4px 8px;
}

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-car text-primary"></i>
            <span class="sidebar-brand">Car Care</span>
        </div>
      <nav class="sidebar-menu">
    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    <!-- Product Management Section -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">CATALOG</h6>
        
        <a href="{{ route('admin.categories.index') }}" class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-folder"></i> Categories
        </a>
        
        <a href="{{ route('admin.products.index') }}" class="menu-item {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> Products
        </a>
        
        <a href="{{ route('admin.products.create') }}" class="menu-item {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Add Product
        </a>
        
        <a href="{{ route('admin.products.low-stock') }}" class="menu-item {{ request()->routeIs('admin.products.low-stock') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Low Stock
            @php
                $lowStockCount = \App\Models\Product::whereColumn('quantity', '<=', 'min_quantity')->count();
            @endphp
            @if($lowStockCount > 0)
                <span class="badge bg-warning ms-auto">{{ $lowStockCount }}</span>
            @endif
        </a>
    </div>
    
    <!-- Marketing Section -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">MARKETING</h6>
        
        <a href="{{ route('admin.coupons.index') }}" class="menu-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt"></i> Coupons
            @php
                $activeCoupons = \App\Models\Coupon::where('status', 'active')->count();
                $expiringSoon = \App\Models\Coupon::where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now())
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->count();
            @endphp
            @if($expiringSoon > 0)
                <span class="badge bg-warning ms-auto">{{ $expiringSoon }}</span>
            @endif
        </a>
        
        <a href="{{ route('admin.coupons.create') }}" class="menu-item {{ request()->routeIs('admin.coupons.create') ? 'active' : '' }}">
            <i class="fas fa-plus-square"></i> Create Coupon
        </a>
        
        <a href="{{ route('admin.coupons.index', ['status' => 'expiring']) }}" class="menu-item">
            <i class="fas fa-clock"></i> Expiring Soon
            @if($expiringSoon > 0)
                <span class="badge bg-warning ms-auto">{{ $expiringSoon }}</span>
            @endif
        </a>
    </div>
    
    <!-- Import/Export Section -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">TOOLS</h6>
        
        <a href="{{ route('admin.products.import') }}" class="menu-item {{ request()->routeIs('admin.products.import*') ? 'active' : '' }}">
            <i class="fas fa-upload"></i> Import Products
        </a>
        
        <a href="{{ route('admin.products.export') }}" class="menu-item">
            <i class="fas fa-download"></i> Export Products
        </a>
        
        <a href="{{ route('admin.coupons.export') }}" class="menu-item">
            <i class="fas fa-ticket-alt"></i> Export Coupons
        </a>
    </div>
    
    <!-- Orders Section (for future) -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">SALES</h6>
        
        <a href="#" class="menu-item">
            <i class="fas fa-shopping-cart"></i> Orders
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
        
        <a href="#" class="menu-item">
            <i class="fas fa-users"></i> Customers
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
    </div>
    
    <!-- Analytics Section (for future) -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">ANALYTICS</h6>
        
        <a href="#" class="menu-item">
            <i class="fas fa-chart-bar"></i> Reports
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
        
        <a href="#" class="menu-item">
            <i class="fas fa-chart-line"></i> Analytics
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
    </div>
    
    <!-- Settings Section -->
    <div class="menu-section">
        <h6 class="menu-section-title px-3 py-2 text-muted small">SETTINGS</h6>
        
        <a href="#" class="menu-item">
            <i class="fas fa-cog"></i> Settings
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
        
        <a href="#" class="menu-item">
            <i class="fas fa-user-shield"></i> Admin Users
            {{-- <span class="badge bg-info ms-auto">Coming Soon</span> --}}
        </a>
    </div>
</nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ Auth::guard('admin')->user()->name }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-area">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // CSRF token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>