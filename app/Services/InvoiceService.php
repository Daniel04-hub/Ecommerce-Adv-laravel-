<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate invoice for an order
     *
     * @param Order $order
     * @return string File path
     */
    public static function generate(Order $order): string
    {
        // Generate invoice HTML
        $html = self::generateInvoiceHtml($order);
        
        // For now, store as HTML (can be converted to PDF later)
        $filename = "invoice-{$order->id}-" . now()->format('Ymd-His') . '.html';
        
        Storage::disk('invoices')->put($filename, $html);
        
        return $filename;
    }

    /**
     * Get invoice path for an order
     *
     * @param Order $order
     * @return string|null
     */
    public static function getInvoicePath(Order $order): ?string
    {
        $files = Storage::disk('invoices')->files();
        
        foreach ($files as $file) {
            if (str_starts_with($file, "invoice-{$order->id}-")) {
                return $file;
            }
        }
        
        return null;
    }

    /**
     * Check if invoice exists for order
     *
     * @param Order $order
     * @return bool
     */
    public static function exists(Order $order): bool
    {
        return self::getInvoicePath($order) !== null;
    }

    /**
     * Download invoice
     *
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public static function download(Order $order)
    {
        $path = self::getInvoicePath($order);
        
        if (!$path) {
            return null;
        }
        
        return FileStorageService::download($path, 'invoices', "invoice-{$order->id}.html");
    }

    /**
     * Generate invoice HTML content
     *
     * @param Order $order
     * @return string
     */
    protected static function generateInvoiceHtml(Order $order): string
    {
        $product = $order->product;
        $customer = $order->customer;
        $paymentMethod = $order->payment_method ?? 'N/A';
        $invoiceDate = $order->created_at->format('F d, Y');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #' . $order->id . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 30px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details th, .details td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .details th { background-color: #f5f5f5; }
        .total { text-align: right; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #: ' . $order->id . '</p>
        <p>Date: ' . $invoiceDate . '</p>
    </div>

    <div class="invoice-info">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>Bill To:</strong><br>
                    ' . $order->full_name . '<br>
                    ' . $order->address . '<br>
                    Phone: ' . $order->phone . '<br>
                    Email: ' . $order->email . '
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Order Status:</strong> ' . $order->status . '<br>
                    <strong>Payment Method:</strong> ' . $paymentMethod . '
                </td>
            </tr>
        </table>
    </div>

    <table class="details">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>' . $product->name . '</td>
                <td>' . $order->quantity . '</td>
                <td>₹' . $product->price . '</td>
                <td>₹' . $order->price . '</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p>Subtotal: ₹' . $order->price . '</p>
        <p>Shipping: ₹0.00 (FREE)</p>
        <p>Tax: Included</p>
        <hr>
        <p style="font-size: 24px;">Total: ₹' . $order->price . '</p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    /**
     * Generate invoice for order if not exists
     *
     * @param Order $order
     * @return string
     */
    public static function generateIfNotExists(Order $order): string
    {
        $path = self::getInvoicePath($order);
        
        if ($path) {
            return $path;
        }
        
        return self::generate($order);
    }
}
