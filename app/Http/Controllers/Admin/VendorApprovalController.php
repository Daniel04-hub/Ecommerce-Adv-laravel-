<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApprovalController extends Controller
{
    public function index()
    {
        return Vendor::with('user')->get();
    }

    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);

        $vendor->update([
            'status' => 'approved',
        ]);

        return response()->json([
            'message' => 'Vendor approved successfully',
        ]);
    }

    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);

        $vendor->update([
            'status' => 'suspended',
        ]);

        return response()->json([
            'message' => 'Vendor suspended successfully',
        ]);
    }
}
