@extends('layouts.admin')

@section('page-title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Vendor Details</h2>
            <p class="text-muted mb-0">View vendor company information</p>
        </div>
        <div>
            <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Company Logo</label>
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 160px; height: 160px; overflow: hidden;">
                        @if(!empty($profile?->company_logo_path))
                            <img src="{{ asset('storage/' . $profile->company_logo_path) }}" alt="Company Logo" style="width: 160px; height: 160px; object-fit: cover;">
                        @else
                            <i class="bi bi-building" style="font-size: 3rem;"></i>
                        @endif
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Company Name</label>
                            <div class="fw-semibold">{{ $vendor->company_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Status</label>
                            <div class="fw-semibold">{{ ucfirst($vendor->status) }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Owner</label>
                            <div class="fw-semibold">{{ $vendor->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Owner Email</label>
                            <div class="fw-semibold">{{ $vendor->user->email ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">GST Number</label>
                            <div class="fw-semibold">{{ $vendor->gst_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Company Email</label>
                            <div class="fw-semibold">{{ $profile->company_email ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Company Phone</label>
                            <div class="fw-semibold">{{ $profile->company_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Vendor Address</label>
                            <div class="fw-semibold">{{ $vendor->address ?? 'N/A' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Company Address</label>
                            <div class="fw-semibold">{{ $profile->company_address ?? 'N/A' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Company Description</label>
                            <div class="fw-semibold">{{ $profile->company_description ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
