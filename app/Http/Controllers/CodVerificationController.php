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

        // OTP verified - update order status to delivered
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'COD order verified successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'delivered_at' => $order->delivered_at,
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
