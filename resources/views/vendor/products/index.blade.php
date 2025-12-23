@extends('layouts.vendor')

@section('page-title', 'Products')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Products</h2>
            <p class="text-muted mb-0">Manage your product inventory</p>
        </div>
        <div>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    @if($products->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4 mb-2">No Products Yet</h4>
                <p class="text-muted mb-4">Start by adding your first product to your inventory.</p>
                <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Your First Product
                </a>
            </div>
        </div>
    @else
        <!-- Products Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3" style="width: 80px;">Image</th>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3" style="width: 120px;">Price</th>
                                <th class="px-4 py-3" style="width: 100px;">Stock</th>
                                <th class="px-4 py-3" style="width: 120px;">Status</th>
                                <th class="px-4 py-3 text-end" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <!-- Product Image -->
                                    <td class="px-4 py-3">
                                        @php
                                            $primaryImage = $product->images()->where('is_primary', true)->first();
                                            $imageUrl = $primaryImage 
                                                ? asset('storage/' . $primaryImage->path) 
                                                : 'https://via.placeholder.com/60x60?text=No+Image';
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded border"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>

                                    <!-- Product Name -->
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                        @if($product->description)
                                            <small class="text-muted">
                                                {{ Str::limit($product->description, 50) }}
                                            </small>
                                        @endif
                                    </td>

                                    <!-- Price -->
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold text-dark">₹{{ number_format($product->price, 2) }}</span>
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-4 py-3">
                                        @if($product->stock > 10)
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $product->stock }} units
                                            </span>
                                        @elseif($product->stock > 0)
                                            <span class="badge bg-warning-subtle text-warning">
                                                {{ $product->stock }} units
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">
                                                Out of stock
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3">
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        @elseif($product->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle"></i> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('vendor.products.edit', $product) }}" 
                                               class="btn btn-outline-primary"
                                               title="Edit Product">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('vendor.products.stock', $product) }}" 
                                               class="btn btn-outline-secondary"
                                               title="Update Stock">
                                                <i class="bi bi-box"></i>
                                            </a>
                                            <form action="{{ route('vendor.products.destroy', $product) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger"
                                                        title="Delete Product">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products Count -->
        <div class="mt-3 text-muted small">
            <i class="bi bi-info-circle"></i> 
            Showing {{ $products->count() }} {{ Str::plural('product', $products->count()) }}
        </div>
    @endif
</div>
@endsection
