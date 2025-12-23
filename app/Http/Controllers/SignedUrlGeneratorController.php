<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\SignedUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignedUrlGeneratorController extends Controller
{
    /**
     * Show signed URL generator page for testing
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Only for authenticated users (optional: restrict to admin)
        $orders = Order::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('signed-urls.generator', compact('orders'));
    }

    /**
     * Generate temporary invoice URL
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInvoiceUrl(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'expires_in' => 'required|integer|min:1|max:1440', // Max 24 hours
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verify user owns the order (for customers)
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        
        if (!$isAdmin && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $url = SignedUrlService::generateInvoiceUrl($order->id, $request->expires_in);

        return response()->json([
            'success' => true,
            'url' => $url,
            'expires_in_minutes' => $request->expires_in,
            'expires_at' => now()->addMinutes($request->expires_in)->toDateTimeString(),
        ]);
    }

    /**
     * Generate temporary shipping label URL
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateShippingLabelUrl(Request $request)
    {
        // Only vendors/admins can generate shipping labels
        $user = Auth::user();
        $hasAccess = in_array($user->role, ['vendor', 'admin']);
        
        abort_if(!$hasAccess, 403);

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'expires_in' => 'required|integer|min:1|max:1440',
        ]);

        $url = SignedUrlService::generateShippingLabelUrl($request->order_id, $request->expires_in);

        return response()->json([
            'success' => true,
            'url' => $url,
            'expires_in_minutes' => $request->expires_in,
            'expires_at' => now()->addMinutes($request->expires_in)->toDateTimeString(),
        ]);
    }
}
