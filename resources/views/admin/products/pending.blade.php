@extends('layouts.app')

@section('content')
<div>
    <h1 class="section-title">Pending Products</h1>

    @if($products->isEmpty())
        <div class="card text-slate-600">No pending products.</div>
    @else
        <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-left">Vendor</th>
                    <th class="text-left">Price</th>
                    <th class="text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->vendor->company_name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.products.approve', $product) }}"
                                  style="display:inline">
                                @csrf
                                <button class="btn-secondary text-sm">Approve</button>
                            </form>

                            <form method="POST"
                                  action="{{ route('admin.products.reject', $product) }}"
                                  style="display:inline">
                                @csrf
                                <button class="btn-primary text-sm">Reject</button>
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
