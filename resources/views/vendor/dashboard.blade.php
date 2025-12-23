@extends('layouts.vendor')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Welcome back! Here's an overview of your business.</p>
        </div>
        <div>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Products Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Products</p>
                            <h2 class="mb-0 fw-bold">{{ $totalProducts ?? 0 }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-seam text-primary fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.products.index') }}" class="text-primary text-decoration-none small">
                            View all products <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Orders</p>
                            <h2 class="mb-0 fw-bold">{{ $totalOrders ?? 0 }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cart-check text-success fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.orders.index') }}" class="text-success text-decoration-none small">
                            View all orders <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Pending Orders</p>
                            <h2 class="mb-0 fw-bold">{{ $ordersByStatus['placed'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.orders.index') }}" class="text-warning text-decoration-none small">
                            Review pending <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Orders by Status</h5>
                </div>
                <div class="card-body">
                    @if(!empty($ordersByStatus) && $ordersByStatus->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($ordersByStatus as $status => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        @if($status === 'placed')
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-cart-plus"></i>
                                            </span>
                                        @elseif($status === 'paid')
                                            <span class="badge bg-success me-2">
                                                <i class="bi bi-credit-card"></i>
                                            </span>
                                        @elseif($status === 'packed')
                                            <span class="badge bg-primary me-2">
                                                <i class="bi bi-box"></i>
                                            </span>
                                        @elseif($status === 'shipped')
                                            <span class="badge bg-warning me-2">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                        @elseif($status === 'delivered')
                                            <span class="badge bg-success me-2">
                                                <i class="bi bi-check-circle"></i>
                                            </span>
                                        @elseif($status === 'cancelled')
                                            <span class="badge bg-danger me-2">
                                                <i class="bi bi-x-circle"></i>
                                            </span>
                                        @endif
                                        <span class="text-capitalize">{{ $status }}</span>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('vendor.products.create') }}" class="btn btn-outline-primary d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">Add New Product</div>
                                <small class="text-muted">List a new product for sale</small>
                            </div>
                        </a>

                        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-list-ul me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">Manage Products</div>
                                <small class="text-muted">View and edit your products</small>
                            </div>
                        </a>

                        <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-success d-flex align-items-center">
                            <i class="bi bi-cart-check me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">View Orders</div>
                                <small class="text-muted">Check and manage orders</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
