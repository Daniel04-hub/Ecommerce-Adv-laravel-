<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
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

        // Additional KPI metrics (UI-only aggregation)
        $lowStockCount = Product::where('vendor_id', $vendorId)
            ->where('stock', '<=', 5)
            ->count();

        $pendingProductsCount = Product::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->count();

        $deliveredOrdersCount = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->count();

        $inProgressOrdersCount = Order::where('vendor_id', $vendorId)
            ->whereIn('status', ['placed', 'accepted', 'shipped'])
            ->count();

        $revenueToday = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('price');

        $ordersThisWeek = Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $revenue30Days = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('price');

        return view('vendor.dashboard', [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'ordersByStatus' => $ordersByStatus,
            'lowStockCount' => $lowStockCount,
            'pendingProductsCount' => $pendingProductsCount,
            'deliveredOrdersCount' => $deliveredOrdersCount,
            'inProgressOrdersCount' => $inProgressOrdersCount,
            'revenueToday' => $revenueToday,
            'ordersThisWeek' => $ordersThisWeek,
            'revenue30Days' => $revenue30Days,
        ]);
    }
}
