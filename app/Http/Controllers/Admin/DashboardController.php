<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function index()
    {
        $vendorStats = [
            'approved' => Vendor::where('status', 'approved')->count(),
            'blocked' => Vendor::where('status', 'blocked')->count(),
            'pending' => Vendor::where('status', 'pending')->count(),
        ];

        $productStats = [
            'approved' => Product::where('status', 'approved')->count(),
            'rejected' => Product::where('status', 'rejected')->count(),
            'pending' => Product::where('status', 'pending')->count(),
        ];

        $orderStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.dashboard', [
            'vendorStats' => $vendorStats,
            'productStats' => $productStats,
            'orderStats' => $orderStats,
        ]);
    }
}
