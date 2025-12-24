@extends('layouts.admin')

@section('page-title', 'Orders Monitoring')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Orders Monitoring</h2>
            <p class="text-muted mb-0">Track all platform orders (read-only view)</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                <!-- Search Input -->
                <div class="col-md-6">
                    <label for="search" class="form-label small fw-semibold text-muted">Search Orders</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               placeholder="Search by order ID or customer email..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-4">
                    <label for="status" class="form-label small fw-semibold text-muted">Filter by Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="placed" {{ request('status') === 'placed' ? 'selected' : '' }}>Placed</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4 mb-2">No Orders Found</h4>
                <p class="text-muted mb-0">No orders match your search criteria.</p>
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
                                <th class="px-4 py-3">Vendor</th>
                                <th class="px-4 py-3 text-center" style="width: 80px;">Qty</th>
                                <th class="px-4 py-3" style="width: 120px;">Amount</th>
                                <th class="px-4 py-3" style="width: 130px;">Status</th>
                                <th class="px-4 py-3 text-center" style="width: 100px;">Action</th>
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
                                                 style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $order->customer->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $order->customer->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Product Name -->
                                    <td class="px-4 py-3">
                                        <div class="fw-medium text-dark">{{ $order->product->name ?? 'N/A' }}</div>
                                        <small class="text-muted">₹{{ number_format($order->price / $order->quantity, 2) }} per unit</small>
                                    </td>

                                    <!-- Vendor Info -->
                                    <td class="px-4 py-3">
                                        @if($order->product && $order->product->vendor)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 28px; height: 28px;">
                                                    <i class="bi bi-shop text-success" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <small class="text-dark">{{ $order->product->vendor->company_name }}</small>
                                            </div>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
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

                                    <!-- View Button -->
                                    <td class="px-4 py-3 text-center">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderModal{{ $order->id }}"
                                                title="View Details">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>

                                <!-- Order Details Modal -->
                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order #{{ $order->id }} Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Customer:</strong><br>
                                                    {{ $order->customer->name ?? 'N/A' }}<br>
                                                    <small class="text-muted">{{ $order->customer->email ?? 'N/A' }}</small>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Product:</strong><br>
                                                    {{ $order->product->name ?? 'N/A' }}
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Vendor:</strong><br>
                                                    {{ $order->product->vendor->company_name ?? 'N/A' }}
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <strong>Quantity:</strong><br>
                                                        {{ $order->quantity }}
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <strong>Total Amount:</strong><br>
                                                        ₹{{ number_format($order->price, 2) }}
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Status:</strong><br>
                                                    <span class="text-capitalize">{{ $order->status }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Order Date:</strong><br>
                                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination & Count -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                <i class="bi bi-info-circle"></i> 
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
