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

        // Match users by email to preserve existing roles
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Create a new user; assign customer role ONLY for new users
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(40)),
            ]);

            // mark email as verified without mass assignment
            $user->email_verified_at = now();
            $user->save();

            // Assign the spatie role without affecting any other setup
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }
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
        return redirect()->route('dashboard');
    }
}
