@extends('layouts.app')

@section('page-title', 'Shop')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Our Products</h1>
        <p class="page-subtitle">Discover our curated collection of quality products</p>
    </div>

    @if($products->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3 mb-2">No Products Available</h4>
                <p class="text-muted mb-0">Check back soon for exciting new products!</p>
            </div>
        </div>
    @else
        <!-- Products Grid -->
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <!-- Product Image Container -->
                        <div class="position-relative overflow-hidden" style="height: 240px; background-color: #f8f9fa;">
                            @if($product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid w-100 h-100" 
                                     style="object-fit: cover; object-position: center;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <div class="text-center">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted small mt-2 mb-0">No image</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Stock Badge -->
                            @if($product->stock <= 0)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger">Out of Stock</span>
                                </div>
                            @elseif($product->stock < 5)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-warning">Only {{ $product->stock }} left</span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="card-body d-flex flex-column">
                            <!-- Product Name -->
                            <h5 class="card-title mb-2">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h5>

                            <!-- Product Description -->
                            @if($product->description)
                                <p class="card-text text-muted small mb-2">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                            @endif

                            <!-- Price -->
                            <p class="card-text mb-3 flex-grow-1">
                                <span class="h5 text-primary fw-bold">₹{{ number_format($product->price, 2) }}</span>
                            </p>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                @auth
                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-primary btn-sm w-100" 
                                                @if($product->stock <= 0) disabled @endif>
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-box-arrow-in-right"></i> Login to Order
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Products Count -->
        <div class="mt-4 text-center text-muted small">
            <i class="bi bi-info-circle"></i> 
            Showing {{ $products->count() }} {{ Str::plural('product', $products->count()) }}
        </div>
    @endif
</div>
@endsection
