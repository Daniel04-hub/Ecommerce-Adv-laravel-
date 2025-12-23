<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'E-Commerce') }} - @yield('page-title', 'Home')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        
        /* Navbar Styling */
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            margin: 0 0.5rem;
            font-weight: 500;
            color: #495057 !important;
            transition: color 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .cart-badge {
            background-color: var(--danger-color);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: 700;
            position: absolute;
            top: -8px;
            right: -8px;
        }
        
        .cart-icon-wrapper {
            position: relative;
            display: inline-block;
        }
        
        /* Main Content */
        main {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }
        
        /* Footer */
        footer {
            background-color: #212529;
            color: #fff;
            padding: 2rem 0;
            margin-top: 4rem;
            border-top: 1px solid #e9ecef;
        }
        
        footer a {
            color: #adb5bd;
            text-decoration: none;
        }
        
        footer a:hover {
            color: #fff;
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Button Styling */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        /* Form Styling */
        .form-control, .form-select {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            padding: 0.625rem 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 0.375rem;
            border: none;
        }
        
        /* Page Title */
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 1rem;
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('products.index') }}">
                    <i class="bi bi-bag-check"></i> ShopHub
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="bi bi-shop"></i> Shop
                            </a>
                        </li>
                        
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                    <div class="cart-icon-wrapper">
                                        <i class="bi bi-cart3"></i>
                                        @if(session('cart') && count(session('cart')) > 0)
                                            <span class="cart-badge">{{ count(session('cart')) }}</span>
                                        @endif
                                    </div>
                                    Cart
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('customer.orders.index') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                                    <i class="bi bi-receipt"></i> Orders
                                </a>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus"></i> Register
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="flex-grow-1">
            <div class="container">
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <h6 class="mb-3">
                            <i class="bi bi-bag-check"></i> ShopHub
                        </h6>
                        <p class="small text-secondary">Your trusted online shopping destination for quality products and great deals.</p>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <h6 class="mb-3">Quick Links</h6>
                        <ul class="list-unstyled small">
                            <li><a href="{{ route('products.index') }}">Shop</a></li>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <h6 class="mb-3">Support</h6>
                        <ul class="list-unstyled small">
                            <li><a href="#">FAQs</a></li>
                            <li><a href="#">Shipping Info</a></li>
                            <li><a href="#">Returns</a></li>
                        </ul>
                    </div>
                </div>
                
                <hr class="bg-secondary-subtle">
                
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="small text-secondary mb-0">&copy; {{ date('Y') }} ShopHub. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="small text-secondary mb-0">Secure shopping | Fast delivery | 24/7 Support</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
