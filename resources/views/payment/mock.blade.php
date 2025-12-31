@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Secure Payment</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Demo Payment Gateway</strong>
                        <p class="mb-0 small mt-2">This is a demonstration payment. All transactions will be marked as successful.</p>
                    </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                    <!-- Payment Summary -->
                    <div class="mb-4">
                        <h6 class="mb-3">Order Details:</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>Order Amount:</strong></p>
                            <p class="display-6 text-primary">₹ {{ number_format($total, 2) }}</p>
                        </div>
                    </div>

                    <!-- Checkout Summary -->
                    <div class="mb-4">
                        <h6 class="mb-2">Delivery To:</h6>
                        <div class="bg-light p-3 rounded small">
                            <p class="mb-1"><strong>{{ $checkout['full_name'] }}</strong></p>
                            <p class="mb-1">{{ $checkout['address'] }}</p>
                            <p class="mb-0">Phone: {{ $checkout['phone'] }}</p>
                        </div>
                    </div>

                    <!-- Payment Method Badge -->
                    <div class="mb-4">
                        <h6 class="mb-2">Payment Method:</h6>
                        <span class="badge bg-success">
                            @if($checkout['payment_method'] === 'cod')
                                Cash on Delivery
                            @else
                                Online Payment (Mock)
                            @endif
                        </span>
                    </div>

                    <hr>

                    <!-- Payment Form -->
                    <form action="{{ route('payment.mock.process') }}" method="POST" id="paymentForm">
                        @csrf

                        @if($checkout['payment_method'] === 'mock_payment')
                            <!-- Fake card form for demo -->
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number (Demo)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="card_number"
                                       placeholder="4111 1111 1111 1111"
                                       value="4111 1111 1111 1111"
                                       disabled>
                                <small class="text-muted">Demo card - all payments will be successful</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry" class="form-label">Expiry Date</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="expiry"
                                           placeholder="MM/YY"
                                           value="12/25"
                                           disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="cvv"
                                           placeholder="123"
                                           value="123"
                                           disabled>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <strong>Complete Payment</strong> - ₹ {{ number_format($total, 2) }}
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Your order will be confirmed for Cash on Delivery.
                            </div>

                            <!-- Auto-submit button for COD -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <strong>Confirm Order</strong> - ₹ {{ number_format($total, 2) }}
                                </button>
                            </div>

                            <!-- Auto-submit script for immediate processing (optional) -->
                            <script>
                                // Uncomment to auto-submit for COD
                                // document.getElementById('paymentForm').submit();
                            </script>
                        @endif
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('checkout.show') }}" class="btn btn-outline-secondary w-100">
                            Back to Checkout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info mt-4 small">
                <strong>🔒 Security Notice:</strong> This is a demo store. All payment data is simulated and not processed through any real payment gateway.
            </div>
        </div>
    </div>
</div>
@endsection
