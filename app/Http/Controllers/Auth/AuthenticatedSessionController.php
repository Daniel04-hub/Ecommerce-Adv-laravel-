<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendLoginAlertEmailJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ Vendor login
        if ($this->userHasRole($user, 'vendor')) {
            return redirect()->intended(route('vendor.dashboard'));
        }

        // ✅ Admin login
        if ($this->userHasRole($user, 'admin')) {
            return redirect()->intended(route('admin.products.pending'));
        }

        // ✅ Customer (default)
        return redirect()->intended(route('products.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function userHasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }

        try {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }
        } catch (\Throwable $e) {
            // Fallback below
        }

        return ($user->role ?? null) === $role;
    }
}
