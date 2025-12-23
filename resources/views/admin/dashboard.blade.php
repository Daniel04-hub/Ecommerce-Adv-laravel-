@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="mb-4">
        <h2 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
        <p class="text-muted mb-0">Here's what's happening with your platform today.</p>
    </div>

    <!-- Statistics Cards Row 1 - Vendors -->
    <div class="row g-4 mb-4">
        <!-- Approved Vendors -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Approved Vendors</p>
                            <h2 class="mb-0 fw-bold text-success">{{ $vendorStats['approved'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-shop-window text-success fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.vendors.index') }}" class="text-success text-decoration-none small">
                            View all vendors <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Vendors -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Pending Vendors</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ $vendorStats['pending'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.vendors.index') }}" class="text-warning text-decoration-none small">
                            Review pending <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked Vendors -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Blocked Vendors</p>
                            <h2 class="mb-0 fw-bold text-danger">{{ $vendorStats['blocked'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-x-circle text-danger fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.vendors.index') }}" class="text-danger text-decoration-none small">
                            View blocked <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 - Products -->
    <div class="row g-4 mb-4">
        <!-- Approved Products -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Approved Products</p>
                            <h2 class="mb-0 fw-bold text-success">{{ $productStats['approved'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-seam text-success fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.products.pending') }}" class="text-success text-decoration-none small">
                            View products <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Products -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Pending Products</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ $productStats['pending'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split text-warning fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.products.pending') }}" class="text-warning text-decoration-none small">
                            Review pending <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Products -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Rejected Products</p>
                            <h2 class="mb-0 fw-bold text-danger">{{ $productStats['rejected'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-x-octagon text-danger fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.products.pending') }}" class="text-danger text-decoration-none small">
                            View rejected <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders by Status Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Orders by Status</h5>
                </div>
                <div class="card-body">
                    @if(!empty($orderStats) && $orderStats->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($orderStats as $status => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        @if($status === 'placed')
                                            <span class="badge bg-warning text-dark me-2">
                                                <i class="bi bi-cart-plus"></i>
                                            </span>
                                        @elseif($status === 'accepted')
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-check-circle"></i>
                                            </span>
                                        @elseif($status === 'shipped')
                                            <span class="badge bg-primary me-2">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                        @elseif($status === 'completed')
                                            <span class="badge bg-success me-2">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </span>
                                        @elseif($status === 'cancelled')
                                            <span class="badge bg-danger me-2">
                                                <i class="bi bi-x-circle"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary me-2">
                                                <i class="bi bi-circle"></i>
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
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                            <i class="bi bi-shop me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">Manage Vendors</div>
                                <small class="text-muted">Approve or block vendors</small>
                            </div>
                        </a>

                        <a href="{{ route('admin.products.pending') }}" class="btn btn-outline-success d-flex align-items-center">
                            <i class="bi bi-box-seam me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">Moderate Products</div>
                                <small class="text-muted">Review pending products</small>
                            </div>
                        </a>

                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-cart-check me-2"></i>
                            <div class="text-start flex-grow-1">
                                <div class="fw-semibold">Monitor Orders</div>
                                <small class="text-muted">Track all platform orders</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
