<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\ProductApproved;
use App\Events\ProductRejected;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'pending')->with(['vendor', 'images']);

        // Search by product name
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('admin.products.pending', compact('products'));
    }

    public function approve(Product $product)
    {
        $before = $product->status;
        $product->update(['status' => 'active']);

        if ($before !== 'active') {
            event(new ProductApproved($product->id));
        }

        return back()->with('success', 'Product approved');
    }

    public function reject(Product $product)
    {
        $before = $product->status;
        $product->update(['status' => 'inactive']);

        if ($before !== 'inactive') {
            event(new ProductRejected($product->id));
        }

        return back()->with('success', 'Product rejected');
    }
}
