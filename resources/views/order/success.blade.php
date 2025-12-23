@extends('layouts.app')

@section('page-title', 'Order Confirmed')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <!-- Success Card -->
            <div class="card border-0 shadow-lg overflow-hidden">
                <!-- Success Header -->
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
                    <!-- Animated Success Icon -->
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; background-color: rgba(255,255,255,0.2); border-radius: 50%; animation: pulse 2s infinite;">
                            <i class="bi bi-check-circle-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>

                    <!-- Thank You Message -->
                    <h1 class="text-white mb-2 fw-bold">Thank You!</h1>
                    <p class="text-white-50 mb-0" style="font-size: 1.1rem;">Your order has been placed successfully</p>
                </div>

                <div class="card-body p-4">
                    <!-- Order Number -->
                    <div class="bg-light p-4 rounded-3 mb-4 text-center">
                        <p class="text-muted mb-2 small">Order Number</p>
                        <h2 class="text-primary fw-bold mb-0">
                            #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>

                    <!-- Status Badges -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-2 text-center">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-credit-card text-success"></i> Payment Status
                                </p>
                                <span class="badge bg-success" style="font-size: 0.9rem;">
                                    <i class="bi bi-check-circle"></i> Successful
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-2 text-center">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-box-seam text-info"></i> Order Status
                                </p>
                                <span class="badge bg-info" style="font-size: 0.9rem;">
                                    <i class="bi bi-hourglass-split"></i> {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Order Details Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 text-muted">
                            <i class="bi bi-receipt"></i> Order Details
                        </h6>
                        
                        <div class="bg-light p-3 rounded-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-muted d-block mb-1">Product</small>
                                            <strong class="text-dark">{{ $order->product->name ?? 'Product' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Quantity</small>
                                    <strong class="text-dark">{{ $order->quantity }}</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block mb-1">Unit Price</small>
                                    <strong class="text-dark">₹{{ number_format($order->price, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="bg-primary bg-opacity-10 p-4 rounded-3 mb-4 border border-primary border-opacity-25">
                        <p class="text-muted mb-2 small">Total Amount</p>
                        <h3 class="text-primary fw-bold mb-0">
                            ₹{{ number_format($order->quantity * $order->price, 2) }}
                        </h3>
                    </div>

                    <!-- Delivery Info -->
                    <div class="mb-4">
                        <h6 class="mb-3 text-muted">
                            <i class="bi bi-geo-alt"></i> Delivery Information
                        </h6>
                        
                        <div class="bg-light p-3 rounded-2">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Shipping Address</small>
                                <strong class="d-block text-dark">{{ $order->full_name }}</strong>
                                <small class="text-muted">{{ $order->address }}</small>
                            </div>
                            <hr class="my-2">
                            <div>
                                <small class="text-muted d-block mb-1">Contact</small>
                                <strong class="text-dark">{{ $order->phone }}</strong>
                                <small class="text-muted d-block">{{ $order->email }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-light border border-success border-opacity-50 mb-4">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle text-success" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong class="text-dark">What's Next?</strong>
                                <p class="text-muted small mb-0 mt-1">
                                    A detailed order confirmation has been sent to <strong>{{ $order->email }}</strong>. You'll receive updates about your shipment status via email and SMS.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-shop"></i> Continue Shopping
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Confirmation Note -->
            <div class="mt-4 text-center">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-envelope-check"></i> 
                    <strong>Confirmation Email:</strong> Check your email at <strong>{{ $order->email }}</strong> for order details.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        }
        50% {
            box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
        }
    }
</style>
@endsection
