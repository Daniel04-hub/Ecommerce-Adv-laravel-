<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @method static string generateIfNotExists(\App\Models\Order $order)
 * @see \App\Services\InvoiceService
 */
class InvoiceController extends Controller
{
    /**
     * Download invoice for customer's order
     *
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Order $order)
    {
        // Ensure customer can only download their own invoices
        abort_if($order->user_id !== Auth::id(), 403, 'Unauthorized access to invoice');

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
     * View invoice in browser
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function view(Order $order)
    {
        // Ensure customer can only view their own invoices
        abort_if($order->user_id !== Auth::id(), 403, 'Unauthorized access to invoice');

        // Generate invoice if not exists
        $path = InvoiceService::generateIfNotExists($order);

        // Get invoice content
        $content = Storage::disk('invoices')->get($path);

        return response($content, 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
