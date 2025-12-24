@extends('layouts.app')

@section('page-title', 'My Orders')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">My Orders</h1>
        <p class="page-subtitle">Track and manage all your orders</p>
    </div>

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-4 mb-2">No Orders Yet</h4>
                <p class="text-muted mb-4">You haven't placed any orders yet. Discover amazing products and place your first order!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop"></i> Start Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Order Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Orders</p>
                            <h4 class="mb-0">{{ count($orders) }}</h4>
                        </div>
                        <i class="bi bi-bag text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">In Progress</p>
                            <h4 class="mb-0">{{ count($orders->where('status', '!=', 'delivered')->where('status', '!=', 'cancelled')) }}</h4>
                        </div>
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Delivered</p>
                            <h4 class="mb-0">{{ count($orders->where('status', 'delivered')) }}</h4>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Cancelled</p>
                            <h4 class="mb-0">{{ count($orders->where('status', 'cancelled')) }}</h4>
                        </div>
                        <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100 transition-all" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <div class="card-body">
                            <!-- Order Header -->
                            <div class="row align-items-start mb-3">
                                <div class="col">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <h6 class="card-title mb-1">Order #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                {{ $order->created_at->format('M d, Y @ h:i A') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge 
                                                @if($order->status === 'placed') bg-warning
                                                @elseif($order->status === 'processing') bg-info
                                                @elseif($order->status === 'accepted') bg-primary
                                                @elseif($order->status === 'shipped') bg-primary
                                                @elseif($order->status === 'completed' || $order->status === 'delivered') bg-success
                                                @elseif($order->status === 'cancelled') bg-danger
                                                @else bg-secondary
                                                @endif
                                            ">
                                                <i class="bi 
                                                    @if($order->status === 'placed') bi-hourglass-split
                                                    @elseif($order->status === 'processing') bi-gear
                                                    @elseif($order->status === 'accepted') bi-check-circle
                                                    @elseif($order->status === 'shipped') bi-truck
                                                    @elseif($order->status === 'completed' || $order->status === 'delivered') bi-check-circle
                                                    @elseif($order->status === 'cancelled') bi-x-circle
                                                    @else bi-info-circle
                                                    @endif
                                                "></i>
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <div class="text-end mt-2">
                                                <span class="h6 text-primary mb-0">₹{{ number_format($order->price, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Product Information -->
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex gap-3">
                                        @php
                                            $image = $order->product?->images?->first();
                                            $imageUrl = $image 
                                                ? asset('storage/' . $image->path) 
                                                : 'https://via.placeholder.com/80x80?text=No+Image';
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                             alt="{{ $order->product?->name }}" 
                                             class="rounded" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('products.show', $order->product) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $order->product?->name ?? 'Product Not Found' }}
                                                </a>
                                            </h6>
                                            <small class="text-muted d-block">by {{ $order->product?->vendor->company_name ?? 'N/A' }}</small>
                                            <small class="text-muted d-block">Qty: <span class="fw-medium">{{ $order->quantity }}</span></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-person"></i> Delivered To
                                            </p>
                                            <small class="d-block"><strong>{{ $order->full_name ?? 'N/A' }}</strong></small>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-telephone"></i> Contact
                                            </p>
                                            <small class="d-block"><strong>{{ Str::limit($order->phone ?? 'N/A', 15) }}</strong></small>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-geo-alt"></i> Delivery Address
                                            </p>
                                            <small class="d-block">{{ Str::limit($order->address ?? 'N/A', 60) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Order Timeline (Status Progress) -->
                            <div class="mb-3">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-diagram-2"></i> Order Status Timeline
                                </p>
                                <div class="d-flex justify-content-between position-relative">
                                    @php
                                        $statuses = ['placed', 'accepted', 'shipped', 'completed'];
                                        $currentIndex = array_search($order->status, $statuses);
                                        if ($currentIndex === false) {
                                            $currentIndex = 0;
                                        }
                                    @endphp
                                    @foreach(['Placed', 'Accepted', 'Shipped', 'Delivered'] as $index => $statusLabel)
                                        <div class="text-center flex-grow-1">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2
                                                    @if($index <= $currentIndex)
                                                        bg-success text-white
                                                    @else
                                                        bg-light text-muted
                                                    @endif
                                                " style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                    @if($index < $currentIndex || ($order->status === 'completed' && $index < 3))
                                                        <i class="bi bi-check"></i>
                                                    @elseif($index === $currentIndex)
                                                        <span class="badge bg-primary" style="animation: pulse 2s infinite;">●</span>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </div>
                                                <small class="text-center" style="font-size: 0.8rem;">{{ $statusLabel }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <a href="{{ route('products.show', $order->product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-seam"></i> View Product
                                </a>
                                @if($order->status === 'completed' || $order->status === 'delivered')
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-repeat"></i> Reorder
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm ms-auto">
                                        <i class="bi bi-download"></i> Invoice
                                    </a>
                                @elseif($order->status !== 'cancelled')
                                    <span class="ms-auto">
                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Track your order">
                                            <i class="bi bi-geo-alt"></i> Track Order
                                        </button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="mt-4 p-3 bg-light rounded text-center">
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle"></i> 
                Showing {{ count($orders) }} {{ Str::plural('order', count($orders)) }}
            </p>
        </div>
    @endif
</div>

<style>
    .transition-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
</style>
@endsection
