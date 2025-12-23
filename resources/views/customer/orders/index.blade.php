@extends('layouts.app')

@section('page-title', 'My Orders')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">My Orders</h1>
        <p class="page-subtitle">Track and manage your orders</p>
    </div>

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-4 mb-2">No Orders Yet</h4>
                <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping now!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Browse Products
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 transition-all" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <div class="card-body">
                            <!-- Order Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">Order #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> 
                                        {{ $order->created_at->format('M d, Y @ h:i A') }}
                                    </small>
                                </div>
                                <span class="badge 
                                    @if($order->status === 'placed') bg-warning
                                    @elseif($order->status === 'processing') bg-info
                                    @elseif($order->status === 'shipped') bg-primary
                                    @elseif($order->status === 'delivered') bg-success
                                    @elseif($order->status === 'cancelled') bg-danger
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <hr class="my-3">

                            <!-- Product Information -->
                            <div class="mb-3">
                                <h6 class="text-muted small mb-2">Product</h6>
                                <div class="d-flex gap-2 align-items-start">
                                    @php
                                        $image = $order->product?->images?->first();
                                        $imageUrl = $image 
                                            ? asset('storage/' . $image->path) 
                                            : 'https://via.placeholder.com/60x60?text=No+Image';
                                    @endphp
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $order->product?->name }}" 
                                         class="rounded" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('products.show', $order->product) }}" 
                                           class="text-decoration-none text-dark fw-medium d-block">
                                            {{ $order->product?->name ?? 'Product Not Found' }}
                                        </a>
                                        <small class="text-muted d-block">SKU: #{{ $order->product_id }}</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Order Details Grid -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Quantity</small>
                                    <strong class="d-block">{{ $order->quantity }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Total Amount</small>
                                    <strong class="d-block text-primary">₹{{ number_format($order->price, 2) }}</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Delivery Address</small>
                                    <small class="d-block">{{ $order->full_name ?? 'Not provided' }}<br>{{ Str::limit($order->address ?? 'N/A', 50) }}</small>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('products.show', $order->product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-seam"></i> View Product
                                </a>
                                @if($order->status === 'delivered')
                                    <a href="#" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-repeat"></i> Reorder
                                    </a>
                                @endif
                            </div>

                            <!-- Status Info -->
                            <div class="alert alert-light mt-3 mb-0 py-2 px-2">
                                <small class="text-muted">
                                    @if($order->status === 'placed')
                                        <i class="bi bi-hourglass-split text-warning"></i> Your order is being processed
                                    @elseif($order->status === 'processing')
                                        <i class="bi bi-gear text-info"></i> Your order is being prepared
                                    @elseif($order->status === 'shipped')
                                        <i class="bi bi-truck text-primary"></i> Your order is on the way
                                    @elseif($order->status === 'delivered')
                                        <i class="bi bi-check-circle text-success"></i> Order delivered successfully
                                    @elseif($order->status === 'cancelled')
                                        <i class="bi bi-x-circle text-danger"></i> Order has been cancelled
                                    @else
                                        <i class="bi bi-info-circle"></i> Order status: {{ ucfirst($order->status) }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="mt-4 text-center">
            <p class="text-muted">Total Orders: {{ count($orders) }}</p>
        </div>
    @endif
</div>

<style>
    .transition-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
