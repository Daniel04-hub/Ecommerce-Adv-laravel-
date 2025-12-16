@extends('layouts.app')

@section('content')
<div class="max-w-xl card">

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 border border-green-200 rounded px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold mb-2">
        {{ $product->name }}
    </h2>

    <p class="text-slate-600 mb-4">
        {{ $product->description }}
    </p>

    <p class="text-lg font-bold text-red-600 mb-2">
        ₹{{ $product->price }}
    </p>

    <p class="text-sm text-slate-500 mb-6">
        Available Stock: {{ $product->stock }}
    </p>

    @auth
    @if(auth()->user()->hasRole('customer'))
    <form method="POST" action="{{ route('orders.store', $product) }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">
                Quantity
            </label>
                 <input type="number"
                   name="quantity"
                   min="1"
                   required
                   class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('quantity')
                <div class="mt-1 text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn-primary">
            Place Order
        </button>
    </form>
    @endif
    @endauth

</div>
@endsection
