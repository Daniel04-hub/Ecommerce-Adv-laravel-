@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Success Message -->
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">
                    <i class="bi bi-check-circle"></i> Order Placed Successfully!
                </h4>
                <p>Thank you for your purchase. Your order has been placed and is being processed.</p>
                @if(session('success'))
                    <p class="mb-0">{{ session('success') }}</p>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Order Summary Card -->
            @php
                $order = App\Models\Order::find(session('order_id'));
            @endphp

            @if($order)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt"></i> Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><small>Order Number</small></p>
                            <p class="fw-bold">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><small>Order Date</small></p>
                            <p class="fw-bold">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Delivery Address -->
                    <div class="mb-3">
                        <p class="text-muted mb-2"><small><i class="bi bi-geo-alt"></i> Delivery Address</small></p>
                        <strong class="d-block">{{ $order->full_name ?? 'Not provided' }}</strong>
                        <small class="d-block text-muted">{{ $order->address ?? 'No address' }}</small>
                        <small class="d-block text-muted"><i class="bi bi-telephone"></i> {{ $order->phone ?? 'No phone' }}</small>
                        <small class="d-block text-muted"><i class="bi bi-envelope"></i> {{ $order->email ?? 'No email' }}</small>
                    </div>

                    <hr>

                    <!-- Product Info -->
                    <div class="mb-3">
                        <p class="text-muted mb-2"><small>Product</small></p>
                        <p class="fw-bold mb-1">{{ $order->product->name ?? 'N/A' }}</p>
                        <small class="text-muted">Quantity: {{ $order->quantity }} × ₹{{ number_format($order->product->price ?? 0, 2) }}</small>
                    </div>

                    <hr>

                    <!-- Total -->
                    <div class="text-end">
                        <p class="text-muted mb-1"><small>Total Amount</small></p>
                        <h5 class="text-primary">₹{{ number_format($order->price, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-hourglass-split"></i> Order Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong>Current Status:</strong> <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                        <p class="mt-2 mb-0"><small>Your order is being processed. You will receive updates via email at <strong>{{ $order->email }}</strong></small></p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-shop"></i> Continue Shopping
                </a>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary flex-grow-1">
                    <i class="bi bi-list-check"></i> View My Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
