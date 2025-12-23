@extends('layouts.app')

@section('page-title', 'Checkout')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Secure Checkout</h1>
        <p class="page-subtitle">Complete your purchase</p>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Checkout Form Section -->
        <div class="col-12 col-lg-8">
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <!-- Delivery Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex align-items-center gap-2 py-3">
                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">1</span>
                            <h5 class="mb-0">Delivery Information</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label fw-medium">Full Name</label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('full_name') is-invalid @enderror" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="{{ old('full_name', $name) }}"
                                       placeholder="Enter your full name"
                                       required>
                                @error('full_name')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <input type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $email) }}"
                                       placeholder="you@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-medium">Phone Number</label>
                                <input type="tel" 
                                       class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="10-digit mobile number"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-medium">Delivery Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="4"
                                          placeholder="Street Address, Apartment, Suite, etc."
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex align-items-center gap-2 py-3">
                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">2</span>
                            <h5 class="mb-0">Select Payment Method</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="payment-methods">
                            <!-- Cash on Delivery Option -->
                            <div class="form-check payment-option p-3 mb-3 border rounded-2" style="cursor: pointer;">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="payment_method" 
                                       id="cod" 
                                       value="cod"
                                       {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label w-100 cursor-pointer" for="cod">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold mb-1">
                                                <i class="bi bi-cash-coin text-success"></i> Cash on Delivery (COD)
                                            </div>
                                            <p class="text-muted small mb-0">Pay when you receive your order. No charges added.</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Online Payment Option -->
                            <div class="form-check payment-option p-3 border rounded-2" style="cursor: pointer;">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="payment_method" 
                                       id="mock_payment" 
                                       value="mock_payment"
                                       {{ old('payment_method') === 'mock_payment' ? 'checked' : '' }}>
                                <label class="form-check-label w-100 cursor-pointer" for="mock_payment">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold mb-1">
                                                <i class="bi bi-credit-card text-info"></i> Online Payment (Demo)
                                            </div>
                                            <p class="text-muted small mb-0">Pay now securely with our demo payment gateway. Instant confirmation.</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Back to Cart
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg ms-auto">
                        <i class="bi bi-lock"></i> Place Order
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <!-- Header -->
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt"></i> Order Summary
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Items List -->
                    <div class="mb-4">
                        <h6 class="mb-3 text-muted">Items</h6>
                        <div style="max-height: 350px; overflow-y: auto;">
                            @foreach($cart as $item)
                                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('products.show', $item['product']) }}" 
                                           class="text-decoration-none text-dark fw-medium small d-block mb-1"
                                           style="font-size: 0.95rem;">
                                            {{ Str::limit($item['product']->name, 35) }}
                                        </a>
                                        <small class="text-muted">
                                            Qty: <span class="fw-medium">{{ $item['quantity'] }}</span>
                                        </small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <div class="fw-medium">₹{{ number_format($item['subtotal'], 2) }}</div>
                                        <small class="text-muted">@ ₹{{ number_format($item['product']->price, 2) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr>

                    <!-- Pricing Breakdown -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                <i class="bi bi-truck"></i> Shipping
                            </span>
                            <span class="badge bg-success">FREE</span>
                        </div>

                        <div class="d-flex justify-content-between pb-3 mb-3 border-bottom">
                            <span class="text-muted">Tax (incl.)</span>
                            <span class="text-muted">Included</span>
                        </div>

                        <!-- Total Amount -->
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total Amount</strong>
                            <div class="text-end">
                                <div class="text-primary fw-bold" style="font-size: 1.5rem;">
                                    ₹{{ number_format($total, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Badge -->
                    <div class="alert alert-light border-0 py-2 px-3 mb-0">
                        <small class="text-muted d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success"></i> 
                            Secure SSL encrypted checkout
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-option {
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .payment-option:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
    }
    
    .payment-option input[type="radio"]:checked ~ label {
        color: inherit;
    }
    
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
@endsection
