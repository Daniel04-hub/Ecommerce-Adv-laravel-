@extends('layouts.app')

@section('content')
<div>
    <div class="toolbar">
        <h2 class="section-title mb-0">My Products</h2>
        <a href="{{ route('vendor.products.create') }}" class="btn-primary">Add Product</a>
    </div>

    @if($products->isEmpty())
        <div class="card text-slate-600">No products found.</div>
    @else
        <div class="card">
            <table class="table divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($products as $product)
                        <tr>
                            <td class="px-4 py-3">{{ $product->name }}</td>
                            <td class="px-4 py-3">₹{{ $product->price }}</td>
                            <td class="px-4 py-3">{{ $product->stock }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : ($product->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-slate-100 text-slate-800') }}">{{ ucfirst($product->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('vendor.products.edit', $product) }}" class="btn-link">Edit</a>
                                <a href="{{ route('vendor.products.stock', $product) }}" class="btn-link">Update Stock</a>
                                <form action="{{ route('vendor.products.destroy', $product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" onclick="return confirm('Delete this product?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
