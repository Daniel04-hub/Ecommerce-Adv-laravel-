<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsApiController extends Controller
{
    /**
     * Display a listing of products
    * 
    * @OA\Get(
    *     path="/api/admin/products",
    *     tags={"Admin"},
    *     summary="List products",
    *     description="Returns all products with vendor and status information.",
    *     security={{"sanctum":{}}},
    *     @OA\Response(response=200, description="Products list")
    * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::with('vendor.user')->latest()->get();

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status,
                'vendor_name' => $product->vendor?->user?->name,
                'created_at' => $product->created_at,
            ];
        });

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Display the specified product
        * 
        * @OA\Get(
        *     path="/api/admin/products/{id}",
        *     tags={"Admin"},
        *     summary="Get product",
        *     description="Returns product details by id.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Product details"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with('vendor.user')->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status,
                'vendor_name' => $product->vendor?->user?->name,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]
        ]);
    }

    /**
     * Approve product
        * 
        * @OA\Post(
        *     path="/api/admin/products/{id}/approve",
        *     tags={"Admin"},
        *     summary="Approve product",
        *     description="Marks a product as approved.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Product approved"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Product approved successfully.'
        ]);
    }

    /**
     * Reject product
        * 
        * @OA\Post(
        *     path="/api/admin/products/{id}/reject",
        *     tags={"Admin"},
        *     summary="Reject product",
        *     description="Marks a product as rejected.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Product rejected"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected successfully.'
        ]);
    }
}
