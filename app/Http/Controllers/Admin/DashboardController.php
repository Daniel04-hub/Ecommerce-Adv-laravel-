<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;

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

        // KPI Metrics
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $pendingApprovals = $vendorStats['pending'] + $productStats['pending'];

        return view('admin.dashboard', [
            'vendorStats' => $vendorStats,
            'productStats' => $productStats,
            'orderStats' => $orderStats,
            'totalUsers' => $totalUsers,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'pendingApprovals' => $pendingApprovals,
        ]);
    }
}
