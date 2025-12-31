<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CodVerificationController extends Controller
{
    /**
     * Generate COD verification OTP for order
     * 
     * @OA\Post(
     *     path="/orders/{order}/cod/generate-otp",
     *     tags={"COD Verification"},
     *     summary="Generate COD verification OTP",
     *     description="Customer generates OTP for delivery person to verify COD payment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="COD verification OTP sent to your email and phone.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=400, description="Invalid order status or not COD")
     * )
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateOtp(Order $order)
    {
        // Verify order belongs to authenticated user
        abort_if($order->user_id !== Auth::id(), 403, 'Unauthorized');

        // Check if order is COD
        abort_if($order->payment_method !== 'COD', 400, 'This order is not COD');

        // Check if order is in appropriate status
        $validStatuses = ['processing', 'shipped', 'out_for_delivery'];
        abort_if(!in_array($order->status, $validStatuses), 400, 'Cannot generate OTP for this order status');

        // Check if OTP already exists
        if (OtpService::exists("order-{$order->id}", 'cod_verification')) {
            $remaining = OtpService::getRemainingTime("order-{$order->id}", 'cod_verification');
            
            return back()->with('info', "OTP already sent. Valid for {$remaining} more seconds.");
        }

        // Generate OTP
        $code = OtpService::generate("order-{$order->id}", 'cod_verification', OtpService::COD_EXPIRY);
        
        // Send via email
        OtpService::sendViaEmail($order->email, $code, 'cod_verification');
        
        // Mock SMS (if phone number exists)
        if ($order->phone) {
            OtpService::sendViaSms($order->phone, $code);
        }

        return back()->with('success', 'COD verification OTP sent to your email and phone.');
    }

    /**
     * Verify COD OTP (called by delivery person)
     * 
     * @OA\Post(
     *     path="/cod/verify",
     *     tags={"COD Verification"},
     *     summary="Verify COD OTP and mark order as delivered",
     *     description="Delivery person verifies OTP to confirm COD payment received",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "otp"},
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="COD order verified successfully."),
     *             @OA\Property(property="order", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="delivered"),
     *                 @OA\Property(property="delivered_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'otp' => 'required|digits:6',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verify OTP
        $valid = OtpService::verify("order-{$order->id}", $request->otp, 'cod_verification', true);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.'
            ], 400);
        }

        // OTP verified - update order status
        $order->update([
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'COD order verified successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
            ]
        ]);
    }

    /**
     * Show COD verification page (for delivery person)
     *
     * @return \Illuminate\View\View
     */
    public function showVerificationForm()
    {
        return view('cod.verify');
    }

    /**
     * Check OTP status
     * 
     * @OA\Get(
     *     path="/orders/{order}/cod/status",
     *     tags={"COD Verification"},
     *     summary="Check COD OTP status",
     *     description="Customer checks if OTP is still active and valid",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP status retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="exists", type="boolean", example=true),
     *             @OA\Property(property="remaining_seconds", type="integer", example=240),
     *             @OA\Property(property="remaining_formatted", type="string", example="4 minutes"),
     *             @OA\Property(property="attempts", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOtpStatus(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $info = OtpService::getInfo("order-{$order->id}", 'cod_verification');

        if (!$info) {
            return response()->json([
                'exists' => false,
                'message' => 'No active OTP found.'
            ]);
        }

        return response()->json([
            'exists' => true,
            'remaining_seconds' => $info['remaining_seconds'],
            'remaining_formatted' => OtpService::formatTimeRemaining($info['remaining_seconds']),
            'attempts' => $info['attempts'],
        ]);
    }
}
