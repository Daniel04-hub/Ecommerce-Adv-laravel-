<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vendor Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
        }

        body {
            overflow-x: hidden;
        }

        .vendor-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: #2c3e50;
            color: #ecf0f1;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .vendor-sidebar .sidebar-header {
            padding: 20px;
            background: #34495e;
            border-bottom: 1px solid #2c3e50;
        }

        .vendor-sidebar .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
        }

        .vendor-sidebar .nav-link {
            color: #bdc3c7;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .vendor-sidebar .nav-link:hover {
            background: #34495e;
            color: #fff;
            border-left-color: #3498db;
        }

        .vendor-sidebar .nav-link.active {
            background: #34495e;
            color: #fff;
            border-left-color: #3498db;
        }

        .vendor-sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .vendor-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            z-index: 999;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .vendor-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 30px;
            min-height: calc(100vh - var(--topbar-height));
            background: #f8f9fa;
        }

        .vendor-topbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vendor-topbar .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .vendor-sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            .vendor-sidebar.show {
                left: 0;
            }
            
            .vendor-topbar {
                left: 0;
            }
            
            .vendor-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="vendor-sidebar" id="vendorSidebar">
        <div class="sidebar-header">
            <a href="{{ route('vendor.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-shop"></i> Vendor Panel
            </a>
        </div>

        <nav class="nav flex-column mt-3">
            <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('vendor.products.index') }}" class="nav-link {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>
            
            <a href="{{ route('vendor.orders.index') }}" class="nav-link {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check"></i>
                <span>Orders</span>
            </a>
        </nav>
    </div>

    <!-- Top Navbar -->
    <div class="vendor-topbar">
        <div>
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="mb-0 d-none d-md-inline">@yield('page-title', 'Dashboard')</h5>
        </div>

        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'V', 0, 1)) }}
            </div>
            <div class="d-none d-md-block">
                <div class="fw-semibold">{{ auth()->user()->name ?? 'Vendor' }}</div>
                <small class="text-muted">Vendor Account</small>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('vendor.profile.edit') }}">
                            <i class="bi bi-building"></i> Company Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="vendor-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('vendorSidebar');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
