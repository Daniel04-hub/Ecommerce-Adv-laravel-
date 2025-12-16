@extends('layouts.app')

@section('content')
<h1 class="section-title">Vendor Orders</h1>

<div class="card overflow-hidden">

    @if($orders->isEmpty())
        <p class="p-6 text-slate-500">No orders yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left">Order ID</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">#{{ $order->id }}</td>
                    <td class="px-4 py-3">{{ $order->product->name }}</td>
                    <td class="px-4 py-3 text-center">{{ $order->quantity }}</td>
                    <td class="px-4 py-3 text-center">₹{{ $order->price }}</td>

                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($order->status === 'placed') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'accepted') bg-blue-100 text-blue-700
                            @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center space-y-1">
                        @if($order->status === 'placed')
                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'accepted']) }}">
                                @csrf
                                <button class="btn-secondary text-sm">
                                    Accept
                                </button>
                            </form>
                        @elseif($order->status === 'accepted')
                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'shipped']) }}">
                                @csrf
                                <button class="btn-secondary text-sm">
                                    Ship
                                </button>
                            </form>
                        @elseif($order->status === 'shipped')
                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'completed']) }}">
                                @csrf
                                <button class="btn-primary text-sm">
                                    Complete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
