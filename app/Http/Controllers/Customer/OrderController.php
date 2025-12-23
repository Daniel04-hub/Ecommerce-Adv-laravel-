<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Jobs\VerifyPaymentJob;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // View customer orders
    public function index()
    {
        $orders = Order::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    // View individual order with real-time status
    public function show(Order $order)
    {
        // Ensure customer can only view their own orders
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('product.images', 'product.vendor');

        return view('customer.orders.show', compact('order'));
    }

    // Place order using route model binding
    public function store(Request $request, Product $product)
    {
        abort_if($product->status !== 'active', 403);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {

                // 🔒 Lock product row
                $lockedProduct = Product::where('id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if ($lockedProduct->stock < $request->quantity) {
                    throw new \RuntimeException('Insufficient stock available');
                }

                // ✅ Reduce stock
                $lockedProduct->decrement('stock', $request->quantity);

                // ✅ Create order
                $order = Order::create([
                    'user_id'    => Auth::id(),
                    'vendor_id'  => $lockedProduct->vendor_id,
                    'product_id' => $lockedProduct->id,
                    'quantity'   => $request->quantity,
                    'price'      => $lockedProduct->price * $request->quantity,
                    'status'     => 'placed',
                ]);

                // ✅ DIRECT JOB DISPATCH AFTER COMMIT (NO EVENTS)
                DB::afterCommit(function () use ($order) {
                    Log::info('VerifyPaymentJob DISPATCHED DIRECTLY', [
                        'order_id' => $order->id,
                    ]);

                    VerifyPaymentJob::dispatch($order->id)
                        ->onQueue(config('queues.payment'));
                });
            });

        } catch (\Throwable $e) {
            return back()
                ->withErrors(['quantity' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('orders.success')
            ->with('success', 'Order placed successfully');
    }

    // Beginner-friendly: accepts product_id without route model binding
    public function storeDirect(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($data) {

                // 🔒 Lock product row
                $product = Product::where('id', $data['product_id'])
                    ->lockForUpdate()
                    ->first();

                abort_if($product->status !== 'active', 403);

                if ($product->stock < $data['quantity']) {
                    throw new \RuntimeException('Insufficient stock available');
                }

                // ✅ Reduce stock
                $product->decrement('stock', $data['quantity']);

                // ✅ Create order
                $order = Order::create([
                    'user_id'    => Auth::id(),
                    'vendor_id'  => $product->vendor_id,
                    'product_id' => $product->id,
                    'quantity'   => $data['quantity'],
                    'price'      => $product->price * $data['quantity'],
                    'status'     => 'placed',
                ]);

                // ✅ DIRECT JOB DISPATCH AFTER COMMIT (NO EVENTS)
                DB::afterCommit(function () use ($order) {
                    Log::info('VerifyPaymentJob DISPATCHED DIRECTLY', [
                        'order_id' => $order->id,
                    ]);

                    VerifyPaymentJob::dispatch($order->id)
                        ->onQueue(config('queues.payment'));
                });
            });

        } catch (\Throwable $e) {
            return back()
                ->withErrors(['quantity' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('orders.success')
            ->with('success', 'Order placed successfully');
    }
}
