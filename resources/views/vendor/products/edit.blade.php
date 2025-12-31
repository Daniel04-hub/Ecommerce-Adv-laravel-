@extends('layouts.vendor')

@section('page-title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Product</h2>
            <p class="text-muted mb-0">Update your product details</p>
        </div>
        <div>
            <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('vendor.products.update', $product) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Name</label>
                    <input id="name" name="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="price">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input id="price" name="price" type="number" step="0.01" class="form-control form-control-lg @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="stock">Stock</label>
                        <input id="stock" name="stock" type="number" class="form-control form-control-lg @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" required>
                        @error('stock')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold" for="status">Status</label>
                    <select id="status" name="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $product->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-circle"></i> Update
                    </button>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <a href="{{ route('vendor.products.stock', $product) }}" class="btn btn-outline-warning">
                        <i class="bi bi-box"></i> Update Stock Only
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
