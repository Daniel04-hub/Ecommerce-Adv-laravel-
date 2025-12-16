@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <h2 class="section-title">Edit Product</h2>

    <form method="POST" action="{{ route('vendor.products.update', $product) }}" class="space-y-4 card">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="pending" {{ old('status', $product->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">Update</button>
            <a href="{{ route('vendor.products.index') }}" class="btn-outline">Cancel</a>
            <a href="{{ route('vendor.products.stock', $product) }}" class="btn-secondary">Update Stock Only</a>
        </div>
    </form>
</div>
@endsection
