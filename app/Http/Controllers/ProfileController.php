<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CustomerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'profile' => $request->user()->customerProfile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        /** @var \App\Models\User $user */
        $user = $request->user();
        $profile = $user->customerProfile;

        if (! $profile) {
            $profile = new CustomerProfile(['user_id' => $user->id]);
        }

        $profile->phone = $validated['phone'] ?? null;
        $profile->address = $validated['address'] ?? null;

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->storePublicly("profile-images/customers/{$user->id}", 'public');
            $profile->profile_image_path = $path;
        }

        $profile->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Log out other sessions for the current user.
     * This supports the existing UI modal that POSTs without a password.
     */
    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (config('session.driver') === 'database') {
            $currentId = $request->session()->getId();
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentId)
                ->delete();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
