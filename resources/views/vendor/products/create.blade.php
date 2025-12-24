@extends('layouts.vendor')

@section('page-title', 'Add Product')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Add Product</h2>
            <p class="text-muted mb-0">Create a new product listing</p>
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
            <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Name</label>
                    <input id="name" name="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="price">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input id="price" name="price" type="number" step="0.01" class="form-control form-control-lg @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="stock">Stock</label>
                        <input id="stock" name="stock" type="number" class="form-control form-control-lg @error('stock') is-invalid @enderror" value="{{ old('stock') }}" required>
                        @error('stock')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold" for="images">Images</label>
                    <input id="images" name="images[]" type="file" multiple class="form-control @error('images') is-invalid @enderror">
                    <div class="form-text">You can upload multiple images (max 2MB each).</div>
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-circle"></i> Save Product
                    </button>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
