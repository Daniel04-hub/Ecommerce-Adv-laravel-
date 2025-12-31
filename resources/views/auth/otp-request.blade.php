<x-guest-layout>
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock" style="font-size: 48px; color: #667eea;"></i>
                        <h3 class="mt-3 fw-bold">Login with OTP</h3>
                        <p class="text-muted">Enter your email to receive a one-time password</p>
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

                    <form method="POST" action="{{ route('otp.send') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="your@email.com"
                                       required 
                                       autofocus>
                            </div>
                            <small class="text-muted">We'll send a 6-digit code to this email</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                            <i class="bi bi-send-fill me-2"></i>Send OTP
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-2">Or login with password</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-key-fill me-2"></i>Login with Password
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-0">Don't have an account? 
                            <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>How OTP Login Works</h6>
                <ol class="mb-0 text-muted small">
                    <li>Enter your registered email address</li>
                    <li>Receive a 6-digit OTP code via email</li>
                    <li>Enter the code to login (valid for 5 minutes)</li>
                </ol>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
