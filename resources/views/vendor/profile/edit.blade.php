@extends('layouts.vendor')

@section('page-title', 'Company Profile')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Company Profile</h2>
            <p class="text-muted mb-0">Update your company information and logo</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('vendor.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="form-label fw-semibold">Company Logo @if(empty($profile?->company_logo_path))<span class="text-danger">*</span>@endif</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 96px; height: 96px; overflow: hidden;">
                            @if(!empty($profile?->company_logo_path))
                                <img src="{{ asset('storage/' . $profile->company_logo_path) }}" alt="Company Logo" style="width: 96px; height: 96px; object-fit: cover;">
                            @else
                                <i class="bi bi-building" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control @error('company_logo') is-invalid @enderror" name="company_logo" accept="image/*">
                            @error('company_logo')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">PNG/JPG up to 4MB.</small>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company Name</label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name', $vendor->company_name) }}" required>
                        @error('company_name')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">GST Number</label>
                        <input type="text" class="form-control @error('gst_number') is-invalid @enderror" name="gst_number" value="{{ old('gst_number', $vendor->gst_number) }}">
                        @error('gst_number')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company Email</label>
                        <input type="email" class="form-control @error('company_email') is-invalid @enderror" name="company_email" value="{{ old('company_email', $profile->company_email ?? '') }}">
                        @error('company_email')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company Phone</label>
                        <input type="text" class="form-control @error('company_phone') is-invalid @enderror" name="company_phone" value="{{ old('company_phone', $profile->company_phone ?? '') }}">
                        @error('company_phone')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Vendor Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2" placeholder="Address used for vendor record">{{ old('address', $vendor->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Company Address</label>
                        <textarea class="form-control @error('company_address') is-invalid @enderror" name="company_address" rows="2">{{ old('company_address', $profile->company_address ?? '') }}</textarea>
                        @error('company_address')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Company Description</label>
                        <textarea class="form-control @error('company_description') is-invalid @enderror" name="company_description" rows="4">{{ old('company_description', $profile->company_description ?? '') }}</textarea>
                        @error('company_description')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
