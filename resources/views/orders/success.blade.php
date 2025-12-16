@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-2">Order placed successfully</h2>
    @if(session('success'))
        <p class="text-green-700 mb-4">{{ session('success') }}</p>
    @endif
    <div class="flex gap-3">
        <a href="{{ route('products.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Continue Shopping</a>
        <a href="{{ route('customer.orders.index') }}" class="px-4 py-2 rounded border">View My Orders</a>
    </div>
</div>
@endsection
