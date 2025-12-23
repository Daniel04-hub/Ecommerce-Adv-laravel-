@extends('layouts.admin')

@section('page-title', 'Products Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Products Management</h2>
            <p class="text-muted mb-0">Review and moderate product listings</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($products->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4 mb-2">No Pending Products</h4>
                <p class="text-muted mb-0">All products have been reviewed. New submissions will appear here.</p>
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
                                <th class="px-4 py-3">Vendor</th>
                                <th class="px-4 py-3" style="width: 120px;">Price</th>
                                <th class="px-4 py-3 text-center" style="width: 100px;">Stock</th>
                                <th class="px-4 py-3" style="width: 120px;">Status</th>
                                <th class="px-4 py-3 text-center" style="width: 200px;">Actions</th>
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

                                    <!-- Vendor Info -->
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="bi bi-shop text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $product->vendor->company_name }}</div>
                                                <small class="text-muted">{{ $product->vendor->user->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold text-dark">₹{{ number_format($product->price, 2) }}</span>
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-4 py-3 text-center">
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

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3">
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        @elseif($product->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @elseif($product->status === 'inactive')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Inactive
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Approve Product">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.products.reject', $product) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" title="Reject Product">
                                                    <i class="bi bi-x-lg"></i> Reject
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
            Showing {{ $products->count() }} pending {{ Str::plural('product', $products->count()) }}
        </div>
    @endif
</div>
@endsection
