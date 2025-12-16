@extends('layouts.app')

@section('content')
<div>
    <h1 class="section-title">Vendor Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card">
            <div class="text-sm text-gray-500">Total Products</div>
            <div class="text-3xl font-bold">{{ $totalProducts ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="text-sm text-gray-500">Total Orders</div>
            <div class="text-3xl font-bold">{{ $totalOrders ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="text-sm text-gray-500">Orders by Status</div>
            <div class="mt-2 space-y-1">
                @forelse(($ordersByStatus ?? []) as $status => $count)
                    <div class="flex justify-between"><span class="capitalize">{{ $status }}</span><span class="font-semibold">{{ $count }}</span></div>
                @empty
                    <div class="text-gray-500">No orders yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('vendor.products.index') }}" class="btn-primary">Manage Products</a>
        <a href="{{ route('vendor.orders.index') }}" class="btn-secondary">View Orders</a>
    </div>
</div>
@endsection
