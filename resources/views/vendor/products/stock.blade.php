@extends('layouts.vendor')

@section('page-title', 'Update Stock')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Update Stock</h2>
            <p class="text-muted mb-0">Adjust inventory for your product</p>
        </div>
        <div>
            <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('vendor.products.stock.update', $product) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Product</label>
                    <div class="form-control-plaintext fw-semibold">{{ $product->name }}</div>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label fw-semibold">Stock</label>
                    <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" class="form-control form-control-lg @error('stock') is-invalid @enderror" />
                    @error('stock')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection