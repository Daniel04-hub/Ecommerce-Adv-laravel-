<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Redirect the user to Google's OAuth consent screen
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            // Retrieve the Google user after authentication
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth failed: '.$e->getMessage());
            return redirect()->route('login')
                ->withErrors(['oauth' => 'Google authentication failed. Please try again.']);
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'No email address was returned from Google.']);
        }

        // Admin-only Google OAuth: must match an existing admin user by email.
        $user = User::where('email', $googleUser->getEmail())->first();
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'No admin account exists for this Google email.']);
        }

        $isAdmin = method_exists($user, 'hasRole')
            ? $user->hasRole('admin')
            : (($user->role ?? null) === 'admin');

        if (! $isAdmin) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'This Google account is not authorized for admin access.']);
        }

        // Log the user in
        Auth::login($user, remember: true);

        // Redirect based on role after login
        return $this->redirectForRole($user);
    }

    protected function redirectForRole(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('vendor')) {
            return redirect()->route('vendor.dashboard');
        }
        // For customers and all other roles, land on product catalog
        return redirect()->route('products.index');
    }
}
