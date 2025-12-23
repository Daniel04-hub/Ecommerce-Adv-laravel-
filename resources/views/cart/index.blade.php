@extends('layouts.app')

@section('page-title', 'Shopping Cart')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Shopping Cart</h1>
        <p class="page-subtitle">Review your items before checkout</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(empty($cart))
        <!-- Empty Cart State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-4 mb-2">Your Cart is Empty</h4>
                <p class="text-muted mb-4">Add some products to get started!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3 text-end" style="width: 100px;">Price</th>
                                        <th class="px-4 py-3 text-center" style="width: 120px;">Quantity</th>
                                        <th class="px-4 py-3 text-end" style="width: 120px;">Subtotal</th>
                                        <th class="px-4 py-3 text-center" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                        <tr>
                                            <!-- Product Info -->
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    @php
                                                        $image = $item['product']->images->first();
                                                        $imageUrl = $image 
                                                            ? asset('storage/' . $image->path) 
                                                            : 'https://via.placeholder.com/50x50?text=No+Image';
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" 
                                                         alt="{{ $item['product']->name }}" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <a href="{{ route('products.show', $item['product']) }}" 
                                                           class="text-decoration-none text-dark fw-medium">
                                                            {{ Str::limit($item['product']->name, 40) }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">SKU: #{{ $item['product']->id }}</small>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Price -->
                                            <td class="px-4 py-3 text-end">
                                                <strong>₹{{ number_format($item['product']->price, 2) }}</strong>
                                            </td>

                                            <!-- Quantity -->
                                            <td class="px-4 py-3 text-center">
                                                <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="d-flex justify-content-center">
                                                    @csrf
                                                    <input type="number" 
                                                           name="quantity" 
                                                           min="1" 
                                                           max="{{ $item['product']->stock }}" 
                                                           value="{{ $item['quantity'] }}"
                                                           class="form-control form-control-sm text-center"
                                                           style="width: 70px;"
                                                           onchange="this.form.submit()">
                                                </form>
                                            </td>

                                            <!-- Subtotal -->
                                            <td class="px-4 py-3 text-end">
                                                <strong class="text-primary">₹{{ number_format($item['subtotal'], 2) }}</strong>
                                            </td>

                                            <!-- Remove Button -->
                                            <td class="px-4 py-3 text-center">
                                                <form action="{{ route('cart.remove', $item['product']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Cart Actions -->
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Clear all items from cart?')">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-receipt"></i> Order Summary
                        </h5>

                        <!-- Items Count -->
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Items ({{ count($cart) }})</span>
                            <span class="fw-medium">₹{{ number_format($total, 2) }}</span>
                        </div>

                        <!-- Shipping -->
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-truck"></i> Shipping
                            </span>
                            <span class="badge bg-success">FREE</span>
                        </div>

                        <!-- Tax Info -->
                        <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                            <span class="text-muted">Tax (incl.)</span>
                            <span class="text-muted small">Included in price</span>
                        </div>

                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <h6 class="mb-0">Total Amount</h6>
                            <h5 class="mb-0 text-primary fw-bold">₹{{ number_format($total, 2) }}</h5>
                        </div>

                        <!-- Checkout Button -->
                        <a href="{{ route('checkout.show') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-lock"></i> Proceed to Checkout
                        </a>

                        <!-- Security Notice -->
                        <div class="alert alert-light mt-3 mb-0">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Secure checkout with SSL encryption
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
