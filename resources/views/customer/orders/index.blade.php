@extends('layouts.app')

@section('content')
<div>
    <div class="toolbar">
        <h1 class="section-title mb-0">My Orders</h1>
        <a href="{{ route('products.index') }}" class="btn-link">Continue Shopping</a>
    </div>

    <div class="card">
        @if($orders->isEmpty())
            <p class="p-6 text-slate-600">You have not placed any orders yet.</p>
        @else
            <table class="table divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">#ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-4 py-3">{{ $order->id }}</td>
                            <td class="px-4 py-3">{{ $order->product->name }}</td>
                            <td class="px-4 py-3">{{ $order->quantity }}</td>
                            <td class="px-4 py-3">₹{{ $order->price }}</td>
                            <td class="px-4 py-3">
                                <span class="badge
                                    {{ $order->status === 'placed' ? 'badge-placed' : '' }}
                                    {{ $order->status === 'accepted' ? 'badge-accepted' : '' }}
                                    {{ $order->status === 'shipped' ? 'badge-shipped' : '' }}
                                    {{ $order->status === 'completed' ? 'badge-completed' : '' }}
                                ">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
