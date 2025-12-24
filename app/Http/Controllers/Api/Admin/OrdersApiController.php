<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersApiController extends Controller
{
    /**
     * Display a listing of orders
    * 
    * @OA\Get(
    *     path="/api/admin/orders",
    *     tags={"Admin"},
    *     summary="List orders",
    *     description="Returns all orders with customer info and status.",
    *     security={{"sanctum":{}}},
    *     @OA\Response(response=200, description="Orders list")
    * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->get();

        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'user' => [
                    'name' => $order->user?->name,
                    'email' => $order->user?->email,
                ],
                'created_at' => $order->created_at,
            ];
        });

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Display the specified order
        * 
        * @OA\Get(
        *     path="/api/admin/orders/{id}",
        *     tags={"Admin"},
        *     summary="Get order",
        *     description="Returns order details by id.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Order details"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city,
                'state' => $order->state,
                'zip_code' => $order->zip_code,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'items' => $order->orderItems,
                'user' => [
                    'name' => $order->user?->name,
                    'email' => $order->user?->email,
                ],
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]
        ]);
    }
}
