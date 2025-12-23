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
