<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShippingLabelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShippingLabelController extends Controller
{
    /**
     * Download shipping label for vendor's order
    * 
    * @OA\Get(
    *     path="/vendor/orders/{order}/shipping-label/download",
    *     tags={"Vendor"},
    *     summary="Download shipping label",
    *     description="Vendor downloads shipping label for their order.",
    *     security={{"sanctum":{}}},
    *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
    *     @OA\Response(response=200, description="File download"),
    *     @OA\Response(response=404, description="Label not found"),
    *     @OA\Response(response=403, description="Unauthorized")
    * )
     *
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Order $order)
    {
        // Ensure vendor can only download labels for their orders
        $user = Auth::user();
        $isVendor = $user->role === 'vendor';
        
        abort_if(!$isVendor, 403, 'Only vendors can download shipping labels');

        // Generate shipping label if not exists
        $path = ShippingLabelService::generateIfNotExists($order);

        // Download label
        $response = ShippingLabelService::download($order);

        if (!$response) {
            abort(404, 'Shipping label not found');
        }

        return $response;
    }

    /**
     * View shipping label in browser
        * 
        * @OA\Get(
        *     path="/vendor/orders/{order}/shipping-label",
        *     tags={"Vendor"},
        *     summary="View shipping label",
        *     description="Vendor views shipping label in browser.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="HTML content"),
        *     @OA\Response(response=404, description="Label not found"),
        *     @OA\Response(response=403, description="Unauthorized")
        * )
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function view(Order $order)
    {
        // Ensure vendor can only view labels for their orders
        $user = Auth::user();
        $isVendor = $user->role === 'vendor';
        
        abort_if(!$isVendor, 403, 'Only vendors can view shipping labels');

        // Generate shipping label if not exists
        $path = ShippingLabelService::generateIfNotExists($order);

        // Get label content
        $content = Storage::disk('shipping')->get($path);

        return response($content, 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Generate shipping label for order
        * 
        * @OA\Post(
        *     path="/vendor/orders/{order}/shipping-label/generate",
        *     tags={"Vendor"},
        *     summary="Generate shipping label",
        *     description="Vendor generates shipping label for an order.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=302, description="Redirect back with success"),
        *     @OA\Response(response=403, description="Unauthorized")
        * )
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate(Order $order)
    {
        $user = Auth::user();
        $isVendor = $user->role === 'vendor';
        
        abort_if(!$isVendor, 403);

        $path = ShippingLabelService::generate($order);

        return redirect()->back()->with('success', 'Shipping label generated successfully');
    }
}
