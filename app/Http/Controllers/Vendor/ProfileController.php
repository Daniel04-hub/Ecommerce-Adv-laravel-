<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Vendor|null $vendor */
        $vendor = $user?->vendor;

        abort_if(! $vendor, 403);

        return view('vendor.profile.edit', [
            'vendor' => $vendor,
            'profile' => $vendor->profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Vendor|null $vendor */
        $vendor = $user?->vendor;

        abort_if(! $vendor, 403);

        $profile = $vendor->profile;

        $logoRequired = ! $profile || empty($profile->company_logo_path);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],

            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:2000'],
            'company_description' => ['nullable', 'string', 'max:5000'],
            'company_logo' => array_values(array_filter([
                $logoRequired ? 'required' : 'nullable',
                'image',
                'max:4096',
            ])),
        ]);

        $vendor->company_name = $validated['company_name'];
        $vendor->gst_number = $validated['gst_number'] ?? null;
        $vendor->address = $validated['address'] ?? null;
        $vendor->save();

        if (! $profile) {
            $profile = new VendorProfile(['vendor_id' => $vendor->id]);
        }

        $profile->company_email = $validated['company_email'] ?? null;
        $profile->company_phone = $validated['company_phone'] ?? null;
        $profile->company_address = $validated['company_address'] ?? null;
        $profile->company_description = $validated['company_description'] ?? null;

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->storePublicly("profile-images/vendors/{$vendor->id}", 'public');
            $profile->company_logo_path = $path;
        }

        $profile->save();

        return Redirect::route('vendor.profile.edit')->with('success', 'Profile updated');
    }
}
