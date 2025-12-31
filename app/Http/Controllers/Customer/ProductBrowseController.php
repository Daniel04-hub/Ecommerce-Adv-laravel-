<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'active')->with(['vendor', 'images']);

        // Search by product name or description
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by vendor
        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->get('vendor'));
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Filter by stock availability
        if ($request->get('in_stock') === '1') {
            $query->where('stock', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $vendors = \App\Models\Vendor::where('status', 'approved')->get();

        return view('products.index', compact('products', 'vendors'));
    }

    public function show(Product $product)
    {
        abort_if($product->status !== 'active', 404);

        return view('products.show', compact('product'));
    }
}
