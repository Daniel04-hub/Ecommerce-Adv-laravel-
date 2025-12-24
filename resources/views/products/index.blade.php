@extends('layouts.app')

@section('page-title', 'Shop')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Our Products</h1>
        <p class="page-subtitle">Discover our curated collection of quality products</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="row g-3 mb-4">
        <!-- Search -->
        <div class="col-12 col-md-6">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" name="search" 
                           placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <!-- Sorting -->
        <div class="col-12 col-md-3">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
                @foreach(['search', 'vendor', 'min_price', 'max_price', 'in_stock'] as $param)
                    @if(request($param))
                        <input type="hidden" name="{{ $param }}" value="{{ request($param) }}">
                    @endif
                @endforeach
                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Sort by</option>
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </form>
        </div>

        <!-- Filters Toggle -->
        <div class="col-12 col-md-3">
            <button class="btn btn-outline-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtersPanel">
                <i class="bi bi-funnel"></i> Filters
            </button>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="collapse mb-4" id="filtersPanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Vendor Filter -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Vendor</label>
                            <select name="vendor" class="form-select form-select-sm">
                                <option value="">All Vendors</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ request('vendor') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Min Price -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-muted">Min Price (₹)</label>
                            <input type="number" name="min_price" class="form-control form-control-sm" 
                                   placeholder="0" value="{{ request('min_price') }}">
                        </div>

                        <!-- Max Price -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-muted">Max Price (₹)</label>
                            <input type="number" name="max_price" class="form-control form-control-sm" 
                                   placeholder="100000" value="{{ request('max_price') }}">
                        </div>

                        <!-- Stock Filter -->
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="in_stock" value="1" 
                                       id="inStock" {{ request('in_stock') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inStock">
                                    In Stock Only
                                </label>
                            </div>
                        </div>

                        <!-- Apply Filters -->
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bi bi-check"></i> Apply
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($products->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3 mb-2">No Products Found</h4>
                <p class="text-muted mb-4">Try adjusting your filters or search terms</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-counterclockwise"></i> Clear Filters
                </a>
            </div>
        </div>
    @else
        <!-- Products Grid -->
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm transition-all" style="transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 1rem 3rem rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                        <!-- Product Image Container -->
                        <div class="position-relative overflow-hidden" style="height: 240px; background-color: #f8f9fa;">
                            @if($product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid w-100 h-100" 
                                     style="object-fit: cover; object-position: center; transition: transform 0.3s ease;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">
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
                            @else
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                            @endif

                            <!-- Vendor Badge -->
                            <div class="position-absolute bottom-0 start-0 m-2">
                                <span class="badge bg-secondary-subtle text-secondary small">
                                    <i class="bi bi-shop"></i> {{ Str::limit($product->vendor->company_name, 12) }}
                                </span>
                            </div>
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

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                <i class="bi bi-info-circle"></i> 
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
