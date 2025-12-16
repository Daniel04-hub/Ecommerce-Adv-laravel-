<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductBrowseController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'active')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        abort_if($product->status !== 'active', 404);

        return view('products.show', compact('product'));
    }
}
