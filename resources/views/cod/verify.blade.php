@extends('layouts.guest')

@section('title', 'COD Verification')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-cash-coin" style="font-size: 48px; color: #28a745;"></i>
                        <h3 class="mt-3 fw-bold">COD Order Verification</h3>
                        <p class="text-muted">For Delivery Personnel</p>
                    </div>

                    <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                    <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                    <form id="codVerifyForm">
                        <div class="mb-4">
                            <label for="order_id" class="form-label fw-semibold">Order ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-hash"></i>
                                </span>
                                <input type="number" 
                                       class="form-control" 
                                       id="order_id" 
                                       name="order_id" 
                                       placeholder="Enter order ID"
                                       required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="otp" class="form-label fw-semibold">Customer's OTP Code</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   id="otp" 
                                   name="otp" 
                                   placeholder="000000"
                                   maxlength="6"
                                   pattern="\d{6}"
                                   style="font-size: 28px; letter-spacing: 8px; font-family: monospace;"
                                   required>
                            <small class="text-muted">Ask customer to share their 6-digit verification code</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3" id="verifyBtn">
                            <i class="bi bi-check-circle-fill me-2"></i>Verify & Complete Delivery
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="bg-light p-3 rounded">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Verification Steps</h6>
                        <ol class="mb-0 text-muted small">
                            <li>Ask customer for their Order ID</li>
                            <li>Customer should check email/SMS for OTP code</li>
                            <li>Customer shares the 6-digit OTP with you</li>
                            <li>Enter Order ID and OTP to verify delivery</li>
                            <li>Collect payment after successful verification</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('codVerifyForm');
    const verifyBtn = document.getElementById('verifyBtn');
    const errorAlert = document.getElementById('errorAlert');
    const successAlert = document.getElementById('successAlert');
    const otpInput = document.getElementById('otp');

    // Only allow digits in OTP
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide alerts
        errorAlert.classList.add('d-none');
        successAlert.classList.add('d-none');
        
        // Disable button
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
        
        const formData = {
            order_id: document.getElementById('order_id').value,
            otp: document.getElementById('otp').value,
        };

        fetch('{{ route("cod.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successAlert.textContent = '✅ ' + data.message + ' - Order #' + data.order.id + ' marked as delivered.';
                successAlert.classList.remove('d-none');
                form.reset();
                
                // Show celebration animation
                setTimeout(() => {
                    alert('🎉 Delivery confirmed! You can now collect payment from the customer.');
                }, 500);
            } else {
                errorAlert.textContent = '❌ ' + data.message;
                errorAlert.classList.remove('d-none');
            }
        })
        .catch(error => {
            errorAlert.textContent = '❌ Verification failed. Please check the Order ID and OTP.';
            errorAlert.classList.remove('d-none');
        })
        .finally(() => {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Verify & Complete Delivery';
        });
    });
});
</script>
@endsection
