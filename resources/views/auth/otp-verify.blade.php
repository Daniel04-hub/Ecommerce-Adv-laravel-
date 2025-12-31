<x-guest-layout>
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-check" style="font-size: 48px; color: #28a745;"></i>
                        <h3 class="mt-3 fw-bold">Verify OTP</h3>
                        <p class="text-muted">Enter the 6-digit code sent to</p>
                        <p class="fw-semibold text-primary">{{ $email }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div id="sessionExpired" class="alert alert-warning d-none" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Session expired. Please <a href="{{ route('otp.request') }}">request a new OTP</a>.
                    </div>

                    <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-4">
                            <label for="otp" class="form-label fw-semibold text-center d-block">Enter OTP Code</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center @error('otp') is-invalid @enderror" 
                                   id="otp" 
                                   name="otp" 
                                   placeholder="000000"
                                   maxlength="6"
                                   pattern="\d{6}"
                                   style="font-size: 32px; letter-spacing: 10px; font-family: monospace;"
                                   required 
                                   autofocus>
                            <small class="text-muted d-block text-center mt-2">Code expires in <span id="timer" class="fw-bold text-danger">5:00</span></small>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 mb-3" id="verifyBtn">
                            <i class="bi bi-check-circle-fill me-2"></i>Verify & Login
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive the code?</p>
                            <button type="button" class="btn btn-outline-primary" id="resendBtn">
                                <i class="bi bi-arrow-clockwise me-2"></i>Resend OTP
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('otp.request') }}" class="text-muted text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>Back to email entry
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Refresh CSRF token every 2 minutes to prevent expiry
    setInterval(function() {
        fetch('/login/otp/verify', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('meta[name="csrf-token"]');
            if (newToken) {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken.content);
                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = newToken.content;
                }
            }
        }).catch(err => console.log('Token refresh skipped'));
    }, 120000); // Every 2 minutes

    // Auto-submit when 6 digits entered
    const otpInput = document.getElementById('otp');
    const otpForm = document.getElementById('otpForm');
    let isSubmitting = false;
    
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6 && !isSubmitting) {
            // Auto-submit immediately when 6 digits entered
            isSubmitting = true;
            document.getElementById('verifyBtn').disabled = true;
            document.getElementById('verifyBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
            otpForm.submit();
        }
    });

    // Handle form submission with error recovery
    otpForm.addEventListener('submit', function(e) {
        if (!isSubmitting) {
            isSubmitting = true;
            document.getElementById('verifyBtn').disabled = true;
            document.getElementById('verifyBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
        }
    });

    // Countdown timer (5 minutes)
    let timeLeft = 300; // 5 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerElement.textContent = 'Expired';
            timerElement.classList.add('text-danger');
            document.getElementById('verifyBtn').disabled = true;
        }
    }, 1000);

    // Resend OTP
    const resendBtn = document.getElementById('resendBtn');
    resendBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        
        fetch('{{ route("otp.resend") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: '{{ $email }}' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // Reset timer
                timeLeft = data.expires_in;
                document.getElementById('verifyBtn').disabled = false;
            } else {
                alert('❌ ' + data.message);
            }
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Resend OTP';
        })
        .catch(error => {
            alert('Failed to resend OTP. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Resend OTP';
        });
    });
});
</script>
</x-guest-layout>
