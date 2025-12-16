@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="section-title">Update Stock</h1>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
    @endif

    <form action="{{ route('vendor.products.stock.update', $product) }}" method="POST" class="space-y-4 card">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-gray-700">Product</label>
            <div class="mt-1">{{ $product->name }}</div>
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
            <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" class="mt-1 block w-full border-gray-300 rounded" />
            @error('stock')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-3">
            <button class="btn-primary" type="submit">Save</button>
            <a href="{{ route('vendor.products.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection