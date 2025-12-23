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

    <div class="row g-4">
        <!-- Product Images Section -->
        <div class="col-12 col-md-6">
            @if($product->images->isNotEmpty())
                <div id="productCarousel" class="carousel slide shadow-sm" data-bs-ride="carousel">
                    <div class="carousel-inner" style="height: 400px; background-color: #f8f9fa;">
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
                                     class="img-thumbnail rounded cursor-pointer" 
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
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

            <!-- Vendor Info -->
            <div class="mb-3">
                <small class="text-muted">
                    <i class="bi bi-shop"></i> Sold by <strong>{{ $product->vendor->company_name }}</strong>
                </small>
            </div>

            <!-- Rating/Review Section (placeholder) -->
            <div class="mb-3">
                <div class="d-flex align-items-center gap-2">
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
            <div class="card border-0 bg-light p-3 mb-4">
                <div class="row">
                    <div class="col-6">
                        <p class="text-muted small mb-1">Price</p>
                        <h3 class="text-primary fw-bold">₹{{ number_format($product->price, 2) }}</h3>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-1">Stock Status</p>
                        @if($product->stock > 10)
                            <p class="mb-0"><span class="badge bg-success">In Stock ({{ $product->stock }})</span></p>
                        @elseif($product->stock > 0)
                            <p class="mb-0"><span class="badge bg-warning">Limited ({{ $product->stock }})</span></p>
                        @else
                            <p class="mb-0"><span class="badge bg-danger">Out of Stock</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="mb-4">
                    <h6 class="mb-2">About this product</h6>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Add to Cart Section -->
            @auth
                <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <select class="form-select" id="quantity" name="quantity" required>
                            @for($i = 1; $i <= min(10, $product->stock); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
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
                    Please <a href="{{ route('login') }}" class="alert-link">login</a> to add items to your cart.
                </div>
            @endauth

            <!-- Return to Shop -->
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </div>

    <!-- Related Products Section (placeholder) -->
    <div class="mt-5 pt-4 border-top">
        <h5 class="mb-4">Related Products</h5>
        <div class="alert alert-light text-center py-4">
            <i class="bi bi-box-seam text-muted"></i>
            <p class="text-muted mb-0 mt-2">More related products coming soon</p>
        </div>
    </div>
</div>
@endsection
