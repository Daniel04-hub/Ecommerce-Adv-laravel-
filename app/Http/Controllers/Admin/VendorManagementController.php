<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorManagementController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('user')->paginate(20);
        return view('admin.vendors.index', compact('vendors'));
    }

    public function approve(Vendor $vendor): RedirectResponse
    {
        $vendor->update(['status' => 'approved']);
        return back()->with('status', 'Vendor approved');
    }

    public function block(Vendor $vendor): RedirectResponse
    {
        $vendor->update(['status' => 'blocked']);
        return back()->with('status', 'Vendor blocked');
    }
}
