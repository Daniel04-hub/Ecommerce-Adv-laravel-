<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
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
        /**
         * @OA\Post(
         *     path="/signed-urls/invoice",
         *     tags={"Mobile"},
         *     summary="Generate temporary invoice URL",
         *     description="Generates a temporary signed URL to view a customer's invoice",
         *     security={{"sanctum":{}}},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"order_id","expires_in"},
         *             @OA\Property(property="order_id", type="integer", example=123),
         *             @OA\Property(property="expires_in", type="integer", example=60, description="Expiry in minutes (max 1440)")
         *         )
         *     ),
         *     @OA\Response(response=200, description="Signed URL generated"),
         *     @OA\Response(response=403, description="Unauthorized"),
         *     @OA\Response(response=422, description="Validation error")
         * )
         */
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'expires_in' => 'required|integer|min:1|max:1440', // Max 24 hours
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verify user owns the order (for customers)
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        /** @var User $authUser */
        $authUser = $user;
        $isAdmin = $authUser->hasRole('admin');
        
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
        /**
         * @OA\Post(
         *     path="/signed-urls/shipping-label",
         *     tags={"Vendor"},
         *     summary="Generate temporary shipping label URL",
         *     description="Generates a temporary signed URL to view a shipping label",
         *     security={{"sanctum":{}}},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"order_id","expires_in"},
         *             @OA\Property(property="order_id", type="integer", example=123),
         *             @OA\Property(property="expires_in", type="integer", example=60, description="Expiry in minutes (max 1440)")
         *         )
         *     ),
         *     @OA\Response(response=200, description="Signed URL generated"),
         *     @OA\Response(response=403, description="Unauthorized"),
         *     @OA\Response(response=422, description="Validation error")
         * )
         */
        // Only vendors/admins can generate shipping labels
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        /** @var User $authUser */
        $authUser = $user;
        $hasAccess = $authUser->hasRole(['vendor', 'admin']);
        
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
