<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderSuccessController extends Controller
{
    /**
     * Display order success page
     */
    public function show(): View|RedirectResponse
    {
        $orderId = session()->get('order_id');

        if (!$orderId) {
            return redirect()->route('products.index')->with('error', 'No order found.');
        }

        $order = Order::with('product')->find($orderId);

        if (!$order) {
            return redirect()->route('products.index')->with('error', 'Order not found.');
        }

        // Forget the session order ID
        session()->forget('order_id');

        return view('order.success', compact('order'));
    }
}
