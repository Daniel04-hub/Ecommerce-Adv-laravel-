<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
    * 
    * @OA\Get(
    *     path="/api/admin/dashboard/stats",
    *     tags={"Admin"},
    *     summary="Get admin dashboard statistics",
    *     description="Returns aggregated platform statistics for the admin dashboard.",
    *     security={{"sanctum":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Statistics payload"
    *     ),
    *     @OA\Response(response=401, description="Unauthenticated"),
    *     @OA\Response(response=403, description="Unauthorized")
    * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'customer');
            })->count(),
            'total_vendors' => User::whereHas('roles', function ($q) {
                $q->where('name', 'vendor');
            })->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::latest()->take(5)->get(),
        ];

        return response()->json($stats);
    }
}
