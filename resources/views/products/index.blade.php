@extends('layouts.app')

@section('content')
<h1 class="section-title">Products</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($products as $product)
        <div class="relative card border border-slate-200">
            <div class="flex items-start justify-between">
                <h3 class="font-semibold text-lg mb-2 text-slate-900">
                    {{ $product->name }}
                </h3>
                <span class="text-sm px-2 py-1 rounded bg-green-100 text-green-800">In Stock: {{ $product->stock }}</span>
            </div>

            <p class="text-red-600 font-bold mb-4 text-xl">
                ₹{{ $product->price }}
            </p>

            <div class="mt-2">
                     <a href="{{ route('products.show', $product) }}"
                         class="btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M15.75 19.5l-6-6 6-6"/></svg>
                    View Product
                </a>
            </div>

            <!-- Full-card click area (kept), container is relative so overlay behaves) -->
            <a href="{{ route('products.show', $product) }}" class="absolute inset-0" aria-label="Open {{ $product->name }}"></a>

            <!-- Plain text link as fallback for visibility -->
            <div class="mt-3">
                <a href="{{ route('products.show', $product) }}" class="btn-link">Open details</a>
            </div>
        </div>
    @empty
        <div class="col-span-full card text-slate-600">
            No products available yet.
        </div>
    @endforelse
</div>
@endsection
