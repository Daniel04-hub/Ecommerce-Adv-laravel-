<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\ShippingLabelService;
use Illuminate\Http\Request;

class SignedDownloadController extends Controller
{
    /**
     * Download invoice via temporary signed URL
     * No authentication required - URL signature is the auth
     *
     * @param Request $request
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadInvoice(Request $request, Order $order)
    {
        // Signature validation is handled by 'signed' middleware
        // No need to check auth - signed URL is the authorization

        // Generate invoice if not exists
        InvoiceService::generateIfNotExists($order);

        // Download invoice
        $response = InvoiceService::download($order);

        if (!$response) {
            abort(404, 'Invoice not found');
        }

        return $response;
    }

    /**
     * Download shipping label via temporary signed URL
     *
     * @param Request $request
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadShippingLabel(Request $request, Order $order)
    {
        // Signature validation handled by middleware

        // Generate label if not exists
        ShippingLabelService::generateIfNotExists($order);

        // Download label
        $response = ShippingLabelService::download($order);

        if (!$response) {
            abort(404, 'Shipping label not found');
        }

        return $response;
    }

    /**
     * View invoice via signed URL in browser
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function viewInvoice(Request $request, Order $order)
    {
        // Generate invoice if not exists
        $path = InvoiceService::generateIfNotExists($order);

        // Get invoice content
        $content = \Illuminate\Support\Facades\Storage::disk('invoices')->get($path);

        return response($content, 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * View shipping label via signed URL in browser
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function viewShippingLabel(Request $request, Order $order)
    {
        // Generate label if not exists
        $path = ShippingLabelService::generateIfNotExists($order);

        // Get label content
        $content = \Illuminate\Support\Facades\Storage::disk('shipping')->get($path);

        return response($content, 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
