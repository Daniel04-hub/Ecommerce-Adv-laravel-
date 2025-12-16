<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \App\Models\Vendor|null $vendor */
        $vendor = $user ? $user->vendor : null;

        /** @var int $vendorId */
        $vendorId = $vendor->id;
        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $totalOrders = Order::where('vendor_id', $vendorId)->count();
        $ordersByStatus = Order::where('vendor_id', $vendorId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('vendor.dashboard', [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }
}
