@extends('layouts.vendor')

@section('page-title', 'Orders')

@section('content')

@include('components.order-status-listener', [
    'orderId' => request('order_id'),
    'userId' => auth()->id(),
    'isVendor' => true
])
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Orders</h2>
            <p class="text-muted mb-0">Manage and track your customer orders</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vendor.orders.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted" for="q">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Customer or Product name">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['placed','accepted','shipped','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted" for="from">From</label>
                    <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted" for="to">To</label>
                    <input type="date" id="to" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                </div>
                <div class="col-md-12">
                    <a href="{{ route('vendor.orders.index') }}" class="text-muted small text-decoration-none">
                        <i class="bi bi-x-circle"></i> Reset filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4 mb-2">No Orders Yet</h4>
                <p class="text-muted mb-0">Your orders will appear here once customers start purchasing.</p>
            </div>
        </div>
    @else
        <!-- Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3" style="width: 100px;">Order ID</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3 text-center" style="width: 80px;">Qty</th>
                                <th class="px-4 py-3" style="width: 120px;">Amount</th>
                                <th class="px-4 py-3" style="width: 130px;">Status</th>
                                <th class="px-4 py-3 text-center" style="width: 180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <!-- Order ID -->
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark border fw-semibold">
                                            #{{ $order->id }}
                                        </span>
                                    </td>

                                    <!-- Customer Info -->
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 36px; height: 36px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $order->user->name ?? 'Guest' }}</div>
                                                <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Product Name -->
                                    <td class="px-4 py-3">
                                        <div class="fw-medium text-dark">{{ $order->product->name }}</div>
                                        <small class="text-muted">₹{{ number_format($order->price / $order->quantity, 2) }} per unit</small>
                                    </td>

                                    <!-- Quantity -->
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $order->quantity }}x
                                        </span>
                                    </td>

                                    <!-- Total Amount -->
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold text-dark">₹{{ number_format($order->price, 2) }}</span>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3">
                                        @if($order->status === 'placed')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock"></i> Placed
                                            </span>
                                        @elseif($order->status === 'accepted')
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-check-circle"></i> Accepted
                                            </span>
                                        @elseif($order->status === 'shipped')
                                            <span class="badge bg-primary">
                                                <i class="bi bi-truck"></i> Shipped
                                            </span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill"></i> Completed
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Cancelled
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-4 py-3 text-center">
                                        @if($order->status === 'placed')
                                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'accepted']) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Accept Order">
                                                    <i class="bi bi-check-lg"></i> Accept
                                                </button>
                                            </form>
                                        @elseif($order->status === 'accepted')
                                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'shipped']) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Mark as Shipped">
                                                    <i class="bi bi-truck"></i> Ship
                                                </button>
                                            </form>
                                        @elseif($order->status === 'shipped')
                                            <form method="POST" action="{{ route('vendor.orders.updateStatus', [$order->id, 'completed']) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Mark as Completed">
                                                    <i class="bi bi-check-circle"></i> Complete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">
                                                <i class="bi bi-dash-circle"></i> No Action
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Orders Count -->
        <div class="mt-3 text-muted small">
            <i class="bi bi-info-circle"></i> 
            Showing {{ $orders->count() }} {{ Str::plural('order', $orders->count()) }}
            @if(request()->hasAny(['q','status','from','to']))
                <span class="ms-2">(filtered)</span>
            @endif
        </div>
    @endif
</div>
@endsection
