<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductApprovalController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'pending')->latest()->get();

        return view('admin.products.pending', compact('products'));
    }

    public function approve(Product $product)
    {
        $product->update(['status' => 'active']);

        return back()->with('success', 'Product approved');
    }

    public function reject(Product $product)
    {
        $product->update(['status' => 'inactive']);

        return back()->with('success', 'Product rejected');
    }
}
