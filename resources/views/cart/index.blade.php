@extends('layouts.app')

@section('page-title', 'Shopping Cart')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Shopping Cart</h1>
        <p class="page-subtitle">Review your items before checkout</p>
    </div>

    <!-- Progress Steps -->
    @if(!empty($cart))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <small class="fw-semibold">Cart</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <small class="text-muted">Checkout</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                <p class="text-muted mb-4">Discover amazing products and add them to your cart!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop"></i> Start Shopping
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3 text-end" style="width: 100px;">Price</th>
                                        <th class="px-4 py-3 text-center" style="width: 140px;">Quantity</th>
                                        <th class="px-4 py-3 text-end" style="width: 130px;">Subtotal</th>
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
                                                        <small class="text-muted">by {{ Str::limit($item['product']->vendor->company_name, 20) }}</small>
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
                                                    <div class="input-group" style="width: 110px;">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decrementQuantity(this)">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" 
                                                               name="quantity" 
                                                               min="1" 
                                                               max="{{ $item['product']->stock }}" 
                                                               value="{{ $item['quantity'] }}"
                                                               class="form-control form-control-sm text-center quantity-input"
                                                               style="border-left: none; border-right: none;"
                                                               onchange="this.form.submit()">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="incrementQuantity(this)">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
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
                <div class="d-flex gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Clear all items from cart?')">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-12 col-lg-4">
                <!-- Summary Card -->
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 80px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-receipt"></i> Order Summary
                        </h5>

                        <!-- Items Count -->
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-bag"></i> Items ({{ count($cart) }})
                            </span>
                            <span class="fw-medium">₹{{ number_format($total, 2) }}</span>
                        </div>

                        <!-- Shipping -->
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-truck"></i> Shipping
                            </span>
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> FREE</span>
                        </div>

                        <!-- Discount Info -->
                        <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-tag"></i> Tax (incl.)
                            </span>
                            <span class="small text-muted">Included in price</span>
                        </div>

                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <h6 class="mb-0">Total Amount</h6>
                            <h5 class="mb-0 text-primary fw-bold">₹{{ number_format($total, 2) }}</h5>
                        </div>

                        <!-- Checkout Button -->
                        <a href="{{ route('checkout.show') }}" class="btn btn-primary w-100 btn-lg mb-3">
                            <i class="bi bi-lock"></i> Proceed to Checkout
                        </a>

                        <!-- Security Notice -->
                        <div class="alert alert-light mb-0">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Secure SSL checkout
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card border-0 bg-light p-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-arrow-repeat text-primary" style="font-size: 1.5rem;"></i>
                                <div class="mt-2 small fw-semibold">Easy Returns</div>
                            </small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-light p-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-primary" style="font-size: 1.5rem;"></i>
                                <div class="mt-2 small fw-semibold">Safe Payment</div>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function incrementQuantity(button) {
        const input = button.parentElement.querySelector('.quantity-input');
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }
    
    function decrementQuantity(button) {
        const input = button.parentElement.querySelector('.quantity-input');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
@endsection
