@extends('layouts.admin')

@section('page-title', 'Vendors Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Vendors Management</h2>
            <p class="text-muted mb-0">Manage and moderate vendor accounts</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.vendors.index') }}" method="GET" class="row g-3">
                <!-- Search Input -->
                <div class="col-md-6">
                    <label for="search" class="form-label small fw-semibold text-muted">Search Vendor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               placeholder="Search by company name or email..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-4">
                    <label for="status" class="form-label small fw-semibold text-muted">Filter by Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($vendors->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-shop text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4 mb-2">No Vendors Found</h4>
                <p class="text-muted mb-0">No vendors match your search criteria.</p>
            </div>
        </div>
    @else
        <!-- Vendors Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3" style="width: 80px;">ID</th>
                                <th class="px-4 py-3">Company Name</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3 text-center" style="width: 100px;">Products</th>
                                <th class="px-4 py-3" style="width: 120px;">Status</th>
                                <th class="px-4 py-3 text-center" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                                <tr>
                                    <!-- Vendor ID -->
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark border fw-semibold">
                                            #{{ $vendor->id }}
                                        </span>
                                    </td>

                                    <!-- Company Name -->
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $vendor->company_name }}</div>
                                        @if($vendor->gst_number)
                                            <small class="text-muted">GST: {{ $vendor->gst_number }}</small>
                                        @endif
                                    </td>

                                    <!-- Owner Info -->
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $vendor->user->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Email -->
                                    <td class="px-4 py-3">
                                        <small class="text-muted">{{ $vendor->user->email ?? 'N/A' }}</small>
                                    </td>

                                    <!-- Products Count -->
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $vendor->products()->count() }}
                                        </span>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3">
                                        @if($vendor->status === 'approved')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </span>
                                        @elseif($vendor->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @elseif($vendor->status === 'blocked')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Blocked
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($vendor->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($vendor->status !== 'approved')
                                                <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success" title="Approve Vendor">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($vendor->status !== 'blocked')
                                                <form action="{{ route('admin.vendors.block', $vendor) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger" title="Block Vendor">
                                                        <i class="bi bi-x-lg"></i> Block
                                                    </button>
                                                </form>
                                            @endif

                                            @if($vendor->status === 'blocked')
                                                <span class="text-muted small">
                                                    <i class="bi bi-dash-circle"></i> Blocked
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
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
                Showing {{ $vendors->firstItem() ?? 0 }} to {{ $vendors->lastItem() ?? 0 }} of {{ $vendors->total() }} vendors
            </div>
            <div>
                {{ $vendors->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
