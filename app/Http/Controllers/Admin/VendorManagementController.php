<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Events\VendorApproved;
use App\Events\VendorSuspended;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user');

        // Search by company name or owner email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $vendors = $query->paginate(20)->withQueryString();
        return view('admin.vendors.index', compact('vendors'));
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user', 'profile']);

        return view('admin.vendors.show', [
            'vendor' => $vendor,
            'profile' => $vendor->profile,
        ]);
    }

    public function approve(Vendor $vendor): RedirectResponse
    {
        $before = $vendor->status;
        $vendor->update(['status' => 'approved']);

        if ($before !== 'approved') {
            event(new VendorApproved($vendor->id));
        }
        return back()->with('status', 'Vendor approved');
    }

    public function block(Vendor $vendor): RedirectResponse
    {
        $before = $vendor->status;
        $vendor->update(['status' => 'suspended']);

        if ($before !== 'suspended') {
            event(new VendorSuspended($vendor->id));
        }

        return back()->with('status', 'Vendor suspended');
    }
}
