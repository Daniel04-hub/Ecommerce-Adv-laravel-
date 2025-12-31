@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Shop</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Images Section -->
        <div class="col-12 col-md-6">
            @if($product->images->isNotEmpty())
                <div id="productCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                    <div class="carousel-inner" style="height: 400px; background-color: #f8f9fa; border-radius: 0.375rem;">
                        @foreach($product->images as $index => $image)
                            <div class="carousel-item @if($index === 0) active @endif h-100">
                                <img src="{{ asset('storage/' . $image->path) }}" 
                                     class="d-block w-100 h-100" 
                                     alt="{{ $product->name }}"
                                     style="object-fit: contain; object-position: center;">
                            </div>
                        @endforeach
                    </div>

                    @if($product->images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>

                <!-- Image Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="row g-2 mt-3">
                        @foreach($product->images as $index => $image)
                            <div class="col-auto">
                                <img src="{{ asset('storage/' . $image->path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-thumbnail rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; transition: opacity 0.3s ease;"
                                     onclick="document.querySelector('#productCarousel').querySelector('.carousel-item:nth-child({{ $index + 1 }})').classList.add('active'); document.querySelectorAll('#productCarousel .carousel-item').forEach((el, i) => { if(i !== {{ $index }}) el.classList.remove('active'); })">
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded shadow-sm" style="height: 400px;">
                    <div class="text-center">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No images available</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Details Section -->
        <div class="col-12 col-md-6">
            <!-- Product Name -->
            <h1 class="mb-3">{{ $product->name }}</h1>

            <!-- Vendor Info Card -->
            <div class="card border-0 bg-light mb-3 p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-shop text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Sold by</small>
                        <strong class="d-block">{{ $product->vendor->company_name }}</strong>
                    </div>
                </div>
            </div>

            <!-- Rating/Review Section -->
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-warning">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </span>
                    <span class="text-muted small">(48 reviews)</span>
                </div>
            </div>

            <!-- Price Section -->
            <div class="card border-0 bg-light p-4 mb-4">
                <div class="row align-items-end">
                    <div class="col-6">
                        <p class="text-muted small mb-1">Price</p>
                        <h3 class="text-primary fw-bold mb-0">₹{{ number_format($product->price, 2) }}</h3>
                    </div>
                    <div class="col-6 text-end">
                        <p class="text-muted small mb-1">Stock Status</p>
                        @if($product->stock > 10)
                            <p class="mb-0"><span class="badge bg-success"><i class="bi bi-check-circle"></i> In Stock</span></p>
                        @elseif($product->stock > 0)
                            <p class="mb-0"><span class="badge bg-warning"><i class="bi bi-exclamation-circle"></i> Only {{ $product->stock }} left</span></p>
                        @else
                            <p class="mb-0"><span class="badge bg-danger"><i class="bi bi-x-circle"></i> Out of Stock</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="mb-4">
                    <h6 class="mb-2 fw-semibold">About this product</h6>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Add to Cart Section -->
            @auth
                <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-semibold">Quantity</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" style="max-width: 60px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" 
                                class="btn btn-primary btn-lg" 
                                @if($product->stock <= 0) disabled @endif>
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i>
                    Please <a href="{{ route('login') }}" class="alert-link fw-semibold">login</a> to add items to your cart.
                </div>
            @endauth

            <!-- Additional Info -->
            <div class="row g-3 mt-3 pt-3 border-top">
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="bi bi-shield-check text-primary"></i>
                        <small>Safe & Secure Payment</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="bi bi-arrow-repeat text-primary"></i>
                        <small>Easy Returns</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="bi bi-truck text-primary"></i>
                        <small>Fast Shipping</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="bi bi-headset text-primary"></i>
                        <small>24/7 Support</small>
                    </div>
                </div>
            </div>

            <!-- Return to Shop -->
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-4">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </div>

    <!-- Related Products Section -->
    @php
        $relatedProducts = \App\Models\Product::where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where('vendor_id', $product->vendor_id)
            ->latest()
            ->limit(4)
            ->get();
    @endphp
    
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5 pt-4 border-top">
            <h5 class="mb-4">More from {{ $product->vendor->company_name }}</h5>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease; cursor: pointer;"
                             onmouseover="this.style.transform='translateY(-5px)'"
                             onmouseout="this.style.transform='translateY(0)'">
                            <div class="position-relative overflow-hidden" style="height: 200px; background-color: #f8f9fa;">
                                @if($related->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $related->images->first()->path) }}" 
                                         alt="{{ $related->name }}" 
                                         class="img-fluid w-100 h-100" 
                                         style="object-fit: cover;">
                                @else
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                @endif
                                @if($related->stock <= 0)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Out of Stock</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('products.show', $related) }}" class="text-decoration-none text-dark">
                                        {{ Str::limit($related->name, 40) }}
                                    </a>
                                </h6>
                                <p class="card-text text-primary fw-bold mb-2">₹{{ number_format($related->price, 2) }}</p>
                                <a href="{{ route('products.show', $related) }}" class="btn btn-sm btn-outline-primary w-100">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }
    
    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
@endsection
