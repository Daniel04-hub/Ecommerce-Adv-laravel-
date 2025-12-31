<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Show checkout form
     */
    public function show(): View|RedirectResponse
    {
        // Require authentication for checkout
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        $cartItems = session()->get('cart', []);

        // Redirect to cart if empty
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('warning', 'Your cart is empty.');
        }

        // Reconstruct cart with product details
        $cart = [];
        $total = 0;

        foreach ($cartItems as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $quantity;
                $cart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        // Get user data if authenticated
        $user = Auth::user();
        $name = $user?->name ?? '';
        $email = $user?->email ?? '';

        return view('checkout.show', compact('cart', 'total', 'name', 'email'));
    }

    /**
     * Process checkout (validate and prepare for payment)
     */
    public function process(Request $request): RedirectResponse
    {
        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate inputs
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,mock_payment',
        ], [
            'full_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'address.required' => 'Please enter your delivery address.',
            'phone.required' => 'Please enter your phone number.',
            'payment_method.required' => 'Please select a payment method.',
        ]);

        // Store checkout data in session
        session()->put('checkout', [
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'payment_method' => $validated['payment_method'],
        ]);

        // Redirect to payment page for both mock payment and COD
        return redirect()->route('payment.mock.show')->with('success', 'Proceeding to payment...');
    }
}
