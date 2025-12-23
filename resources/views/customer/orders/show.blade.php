@extends('layouts.app')

@section('page-title', 'Order Details')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Order #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</h1>
        <p class="page-subtitle">Real-time order tracking</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Order Status Timeline -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Status Timeline</h5>

                    <!-- Live Status Badge -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <h6 class="mb-0">Current Status</h6>
                        <span class="badge bg-warning" data-order-status="{{ $order->id }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <!-- Timeline Progress -->
                    <div data-timeline="{{ $order->id }}" class="timeline-container">
                        @php
                            $timeline = [
                                'placed' => ['label' => 'Order Placed', 'icon' => 'bi-hourglass-split'],
                                'accepted' => ['label' => 'Accepted', 'icon' => 'bi-check-circle'],
                                'shipped' => ['label' => 'Shipped', 'icon' => 'bi-truck'],
                                'completed' => ['label' => 'Delivered', 'icon' => 'bi-box-seam'],
                            ];
                        @endphp

                        <div class="d-flex justify-content-between position-relative">
                            @foreach($timeline as $status => $info)
                                <div class="timeline-step {{ $order->status === $status || in_array($status, array_keys(array_slice($timeline, 0, array_search($order->status, array_keys($timeline)) + 1))) ? 'completed' : '' }}">
                                    <div class="step-circle">
                                        <i class="bi {{ $info['icon'] }}"></i>
                                    </div>
                                    <p class="step-label">{{ $info['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt"></i> Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-start">
                        @php
                            $image = $order->product?->images?->first();
                            $imageUrl = $image 
                                ? asset('storage/' . $image->path) 
                                : 'https://via.placeholder.com/100x100?text=No+Image';
                        @endphp
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $order->product?->name }}" 
                             class="rounded" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-2">{{ $order->product?->name ?? 'Product Not Found' }}</h6>
                            <div class="row g-3 text-sm">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Quantity</small>
                                    <strong>{{ $order->quantity }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Unit Price</small>
                                    <strong>₹{{ number_format($order->product?->price ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block mb-1">Subtotal</small>
                            <strong class="text-primary" style="font-size: 1.2rem;">
                                ₹{{ number_format($order->price, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Order Info -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Order Date</small>
                        <strong class="d-block">{{ $order->created_at->format('M d, Y') }}</strong>
                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Order ID</small>
                        <strong class="d-block font-monospace">{{ $order->id }}</strong>
                    </div>

                    <hr>

                    <!-- Pricing -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Subtotal</small>
                            <strong>₹{{ number_format($order->price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Shipping</small>
                            <span class="badge bg-success">FREE</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Tax</small>
                            <small class="text-muted">Incl.</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total</strong>
                        <strong class="text-primary" style="font-size: 1.3rem;">
                            ₹{{ number_format($order->price, 2) }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt"></i> Delivery Address
                    </h5>
                </div>
                <div class="card-body">
                    <strong class="d-block mb-2">{{ $order->full_name ?? 'Not provided' }}</strong>
                    <small class="d-block text-muted mb-2">{{ $order->address ?? 'No address' }}</small>
                    <small class="d-block text-muted mb-3">
                        <i class="bi bi-telephone"></i> {{ $order->phone ?? 'No phone' }}
                    </small>
                    <small class="d-block text-muted">
                        <i class="bi bi-envelope"></i> {{ $order->email ?? 'No email' }}
                    </small>
                </div>
            </div>

            <!-- Download Documents -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Invoice Download -->
                        <a href="{{ route('customer.orders.invoice.download', $order) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download"></i> Download Invoice
                        </a>
                        
                        <!-- View Invoice -->
                        <a href="{{ route('customer.orders.invoice.view', $order) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye"></i> View Invoice
                        </a>

                        <!-- Generate Temporary Share Link -->
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#shareInvoiceModal">
                            <i class="bi bi-share"></i> Generate Share Link
                        </button>

                        <!-- COD Verification OTP -->
                        @if($order->payment_method === 'COD' && in_array($order->status, ['processing', 'shipped', 'out_for_delivery']))
                            <form action="{{ route('cod.generate', $order) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-shield-check"></i> Generate COD Verification OTP
                                </button>
                            </form>
                            <div class="alert alert-warning py-2 px-2 mb-0 mt-2">
                                <small>
                                    <i class="bi bi-info-circle"></i> Share this OTP with delivery person to verify COD payment
                                </small>
                            </div>
                        @endif

                        @if($order->status === 'shipped' || $order->status === 'completed')
                            <div class="alert alert-info py-2 px-2 mb-0 mt-2">
                                <small>
                                    <i class="bi bi-info-circle"></i> Tracking: TRK{{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Broadcasting Listener -->
    @include('components.order-status-listener', [
        'orderId' => $order->id,
        'userId' => auth()->id(),
        'isVendor' => auth()->user()?->hasRole('vendor') ?? false,
    ])
</div>

<style>
    .timeline-container {
        position: relative;
        padding: 20px 0;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        top: 30px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e0e0e0;
        z-index: 0;
    }

    .timeline-container > div {
        position: relative;
        z-index: 1;
    }

    .timeline-step {
        flex: 1;
        text-align: center;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        margin: 0 auto 10px;
        background: white;
        border: 3px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .timeline-step.completed .step-circle {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
    }

    .timeline-step.completed .step-label {
        color: #0d6efd;
        font-weight: 600;
    }
</style>

<!-- Share Invoice Modal -->
<div class="modal fade" id="shareInvoiceModal" tabindex="-1" aria-labelledby="shareInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareInvoiceModalLabel">
                    <i class="bi bi-share"></i> Generate Temporary Share Link
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Create a secure, temporary link to share your invoice with others.</p>
                
                <div class="mb-3">
                    <label for="expiryTime" class="form-label">Link Expires In:</label>
                    <select class="form-select" id="expiryTime">
                        <option value="60">1 Hour</option>
                        <option value="360">6 Hours</option>
                        <option value="720">12 Hours</option>
                        <option value="1440">24 Hours</option>
                    </select>
                </div>

                <div id="generatedLinkContainer" class="d-none">
                    <div class="alert alert-success">
                        <strong>Link Generated!</strong>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="generatedLink" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard()">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> Expires: <span id="expiryTime"></span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="generateLinkBtn">
                    <i class="bi bi-link-45deg"></i> Generate Link
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('generateLinkBtn').addEventListener('click', function() {
    const btn = this;
    const orderId = {{ $order->id }};
    const expiresIn = document.getElementById('expiryTime').value;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
    
    // Generate signed URL via API (you can create an API endpoint for this)
    const url = @json(\App\Services\SignedUrlService::generateInvoiceUrl($order->id, 60));
    
    setTimeout(() => {
        document.getElementById('generatedLink').value = url;
        document.getElementById('generatedLinkContainer').classList.remove('d-none');
        document.getElementById('expiryTime').textContent = new Date(Date.now() + (expiresIn * 60000)).toLocaleString();
        
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check"></i> Generated';
        btn.classList.replace('btn-primary', 'btn-success');
    }, 500);
});

function copyToClipboard() {
    const input = document.getElementById('generatedLink');
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target.closest('button');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
    btn.classList.replace('btn-outline-primary', 'btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.classList.replace('btn-success', 'btn-outline-primary');
    }, 2000);
}
</script>
@endsection
