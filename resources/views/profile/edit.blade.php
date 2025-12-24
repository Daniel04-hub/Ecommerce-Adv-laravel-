@extends('layouts.app')

@section('page-title', 'My Profile')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">My Profile</h1>
        <p class="page-subtitle">Manage your account settings and preferences</p>
    </div>

    <!-- Alerts -->
    @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle"></i> Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle"></i> Password updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Profile Section Tabs -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                <i class="bi bi-person"></i> Account Information
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                <i class="bi bi-lock"></i> Security
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button" role="tab" aria-controls="danger" aria-selected="false">
                <i class="bi bi-exclamation-triangle"></i> Danger Zone
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="profileTabsContent">
        <!-- Account Information Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-circle"></i> Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Update your account's profile information and email address.</p>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="alert alert-info mt-3">
                                    <small>
                                        <i class="bi bi-info-circle"></i> Your email address is unverified.
                                        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 ms-1 text-decoration-none">Click here to verify</button>
                                        </form>
                                    </small>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <div class="alert alert-success mt-3 mb-0">
                                        <small><i class="bi bi-check-circle"></i> Verification link sent to your email.</small>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lock"></i> Update Password
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Ensure your account is using a long, random password to stay secure.</p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Current Password</label>
                            <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password" required>
                            @error('current_password')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password" required>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-lock"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Login Sessions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-diagram-2"></i> Active Sessions
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Manage your login sessions across all devices.</p>
                    <div class="alert alert-light">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Currently logged in on this device. Log out from other sessions if you suspect unauthorized access.
                        </small>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right"></i> Log Out All Other Sessions
                    </button>
                </div>
            </div>
        </div>

        <!-- Danger Zone Tab -->
        <div class="tab-pane fade" id="danger" role="tabpanel" aria-labelledby="danger-tab">
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger bg-opacity-10 border-danger border-opacity-25">
                    <h5 class="card-title mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Delete Account
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Once you delete your account, there is no going back. Please be certain.
                    </p>
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-exclamation-circle"></i> 
                            <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                        </small>
                    </div>

                    <form method="POST" action="{{ route('profile.destroy') }}" class="d-inline" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This cannot be undone.')">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="password_delete" class="form-label fw-semibold">Confirm with Password</label>
                            <input type="password" class="form-control form-control-lg @error('password', 'userDeletion') is-invalid @enderror" id="password_delete" name="password" placeholder="Enter your password to confirm" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Out All Other Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Are you sure you want to log out from all other sessions? You will remain logged in on this device.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('profile.logout-other-sessions') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Log Out All Others
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    .tab-content {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>
@endsection
