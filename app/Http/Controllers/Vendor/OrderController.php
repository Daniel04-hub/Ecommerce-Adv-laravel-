<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OrderStatusService;


class OrderController extends Controller
{
    // View vendor orders
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \App\Models\Vendor|null $vendor */
        $vendor = $user ? $user->vendor : null;

        abort_if(!$vendor, 403);

        /** @var int $vendorId */
        $vendorId = $vendor->id;

        $query = Order::where('vendor_id', $vendorId)
            ->with(['user', 'product'])
            ->latest();

        // Optional filters (status, search, date range)
        if ($status = trim((string) $request->input('status', ''))) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->get();

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

        // Update via service to ensure event dispatch (triggers shipping email when status becomes shipped)
        abort_if(!OrderStatusService::update($order, $status), 422);

        return back()->with('success', 'Order status updated');
    }
}
