<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index(): View
    {
        $cartItems = session()->get('cart', []);
        $total = 0;

        // Reconstruct cart with product details
        $cart = [];
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

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Product $product, Request $request): RedirectResponse
    {
        abort_if($product->status !== 'active', 404);

        $quantity = (int) $request->input('quantity', 1);
        $quantity = max(1, min($quantity, $product->stock));

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            // Update quantity if product already in cart
            $cart[$product->id] += $quantity;
            // Don't exceed available stock
            $cart[$product->id] = min($cart[$product->id], $product->stock);
        } else {
            // Add new product to cart
            $cart[$product->id] = $quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', $product->name . ' added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Product $product, Request $request): RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->remove($product);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            // Limit quantity to available stock
            $cart[$product->id] = min($quantity, $product->stock);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }

    /**
     * Remove product from cart
     */
    public function remove(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
