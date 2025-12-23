<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Events\OrderPlaced;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show mock payment page (for online payment method)
     */
    public function mockShow(): View|RedirectResponse
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to complete payment.');
        }

        $checkout = session()->get('checkout');
        $cartItems = session()->get('cart', []);

        if (!$checkout || empty($cartItems)) {
            return redirect()->route('checkout.show')->with('error', 'Invalid checkout session.');
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }

        return view('payment.mock', compact('checkout', 'total'));
    }

    /**
     * Process mock payment and create order
     */
    public function mockProcess(): RedirectResponse
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to complete payment.');
        }

        $checkout = session()->get('checkout');
        $cartItems = session()->get('cart', []);

        if (!$checkout || empty($cartItems)) {
            return redirect()->route('checkout.show')->with('error', 'Invalid checkout session.');
        }

        try {
            // Calculate total
            $total = 0;
            $orderItems = [];

            foreach ($cartItems as $productId => $quantity) {
                $product = Product::find($productId);

                if (!$product) {
                    throw new \Exception("Product not found: $productId");
                }

                // Check stock availability
                if ($product->stock < $quantity) {
                    throw new \Exception("{$product->name} has insufficient stock.");
                }

                $subtotal = $product->price * $quantity;
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Process each cart item - create one order per product (per existing schema)
            $orders = [];
            foreach ($orderItems as $item) {
                $product = Product::find($item['product_id']);

                $singleOrder = Order::create([
                    'user_id' => Auth::id(),
                    'vendor_id' => $product->vendor_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'status' => 'placed',
                ]);

                // Reduce product stock
                $product?->decrement('stock', $item['quantity']);

                $orders[] = $singleOrder;
            }

            // Fire OrderPlaced event if exists
            if (class_exists('App\Events\OrderPlaced')) {
                foreach ($orders as $ord) {
                    OrderPlaced::dispatch($ord->id);
                }
            }

            // Clear cart and checkout session
            session()->forget(['cart', 'checkout']);

            // Store first order ID in session for success page
            if (!empty($orders)) {
                session()->put('order_id', $orders[0]->id);
            }

            return redirect()->route('order.success')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            return redirect()->route('checkout.show')
                    ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
