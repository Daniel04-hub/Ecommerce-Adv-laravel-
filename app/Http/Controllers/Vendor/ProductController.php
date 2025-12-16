<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'Vendor profile not found');
        }

        $products = $vendor->products()->latest()->get();

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        return view('vendor.products.create');
    }

    public function store(Request $request)
    {
      $vendor = Auth::user()->vendor;
      
        if (!$vendor) {
            abort(403, 'Vendor profile not found');
        }

        // ✅ Validation (STEP 4 added)
        $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:2048',
        ]);

        // ✅ Create product
        $product = $vendor->products()->create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'status'      => 'pending',
        ]);

        // ✅ STEP 4 — Image upload logic
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'path'       => $path,
                    'is_primary' => $index === 0, // first image = main image
                ]);
            }
        }

        return redirect()->route('vendor.products.index');
    }

    public function edit(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($product->vendor_id !== $vendorId, 403);
        return view('vendor.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($product->vendor_id !== $vendorId, 403);

        $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'status'      => 'required|in:pending,active,inactive',
        ]);

        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'status'      => $request->status,
        ]);

        return redirect()->route('vendor.products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($product->vendor_id !== $vendorId, 403);
        $product->delete();
        return back()->with('success', 'Product deleted');
    }

    public function editStock(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($product->vendor_id !== $vendorId, 403);
        return view('vendor.products.stock', compact('product'));
    }

    public function updateStock(Request $request, Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var int $vendorId */
        $vendorId = $user && $user->vendor ? $user->vendor->id : 0;
        abort_if($product->vendor_id !== $vendorId, 403);
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);
        $product->update(['stock' => $validated['stock']]);
        return redirect()->route('vendor.products.index')->with('status', 'Stock updated');
    }
}
