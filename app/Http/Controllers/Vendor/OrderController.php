<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    // View vendor orders
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \App\Models\Vendor|null $vendor */
        $vendor = $user ? $user->vendor : null;

        abort_if(!$vendor, 403);

        /** @var int $vendorId */
        $vendorId = $vendor->id;
        $orders = Order::where('vendor_id', $vendorId)
            ->latest()
            ->get();

        return view('vendor.orders.index', compact('orders'));
    }

    // Update order status
    public function updateStatus(Order $order, string $status)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($order->vendor_id !== $vendorId, 403);

        $allowed = ['accepted', 'shipped', 'completed'];
        abort_if(!in_array($status, $allowed), 400);

        abort_if(!$order->canTransitionTo($status), 422);

        $order->update(['status' => $status]);

        return back()->with('success', 'Order status updated');
    }
}
