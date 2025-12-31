@extends('layouts.app')

@section('page-title', 'Invalid Link')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-5">
                    <!-- Invalid Icon -->
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-3">Invalid Link</h2>
                    
                    <p class="text-muted mb-4">
                        {{ $message ?? 'This link is invalid or has been tampered with.' }}
                    </p>

                    <div class="alert alert-danger">
                        <i class="bi bi-shield-exclamation"></i> 
                        {{ $hint ?? 'Please ensure you used the complete link from your email.' }}
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        @auth
                            <a href="{{ route('customer.orders.index') }}" class="btn btn-primary">
                                <i class="bi bi-receipt"></i> View My Orders
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login to Your Account
                            </a>
                        @endauth
                        
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Shop
                        </a>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            If you continue to experience issues, please contact support.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
