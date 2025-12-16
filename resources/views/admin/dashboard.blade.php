@extends('layouts.app')

@section('content')
<div>
    <h1 class="section-title">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card">
            <div class="text-sm text-gray-500">Vendors</div>
            <div class="mt-2 space-y-1">
                <div class="flex justify-between"><span>Approved</span><span class="font-semibold">{{ $vendorStats['approved'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span>Blocked</span><span class="font-semibold">{{ $vendorStats['blocked'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span>Pending</span><span class="font-semibold">{{ $vendorStats['pending'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="text-sm text-gray-500">Products</div>
            <div class="mt-2 space-y-1">
                <div class="flex justify-between"><span>Approved</span><span class="font-semibold">{{ $productStats['approved'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span>Rejected</span><span class="font-semibold">{{ $productStats['rejected'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span>Pending</span><span class="font-semibold">{{ $productStats['pending'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="text-sm text-gray-500">Orders by Status</div>
            <div class="mt-2 space-y-1">
                @forelse(($orderStats ?? []) as $status => $count)
                    <div class="flex justify-between"><span class="capitalize">{{ $status }}</span><span class="font-semibold">{{ $count }}</span></div>
                @empty
                    <div class="text-gray-500">No orders yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.vendors.index') }}" class="btn-secondary">Manage Vendors</a>
        <a href="{{ route('admin.products.pending') }}" class="btn-primary">Moderate Products</a>
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Monitor Orders</a>
    </div>
</div>
@endsection