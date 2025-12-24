<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SignedUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignedUrlGeneratorController extends Controller
{
    /**
     * Generate temporary signed URL for invoice download
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInvoiceUrl(Request $request, Order $order)
    {
        // Ensure customer can only generate links for their own orders
        abort_if($order->user_id !== Auth::id(), 403, 'Unauthorized');

        $validated = $request->validate([
            'expires_in' => 'required|integer|min:5|max:1440', // 5 minutes to 24 hours
        ]);

        $url = SignedUrlService::generateInvoiceUrl($order->id, $validated['expires_in']);

        return response()->json([
            'url' => $url,
            'expires_at' => now()->addMinutes($validated['expires_in'])->toIso8601String(),
        ]);
    }

    /**
     * Generate temporary signed URL for shipping label download
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateShippingLabelUrl(Request $request, Order $order)
    {
        // Only vendors can generate shipping label links
        abort_if(!Auth::user()->hasRole('vendor'), 403, 'Unauthorized');

        $validated = $request->validate([
            'expires_in' => 'required|integer|min:5|max:1440',
        ]);

        $url = SignedUrlService::generateShippingLabelUrl($order->id, $validated['expires_in']);

        return response()->json([
            'url' => $url,
            'expires_at' => now()->addMinutes($validated['expires_in'])->toIso8601String(),
        ]);
    }
}
