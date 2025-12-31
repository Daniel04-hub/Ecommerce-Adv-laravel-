<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class ShippingLabelService
{
    /**
     * Generate shipping label for an order
     *
     * @param Order $order
     * @return string File path
     */
    public static function generate(Order $order): string
    {
        // Generate shipping label HTML
        $html = self::generateShippingLabelHtml($order);
        
        $filename = "shipping-label-{$order->id}-" . now()->format('Ymd-His') . '.html';
        
        Storage::disk('shipping')->put($filename, $html);
        
        return $filename;
    }

    /**
     * Get shipping label path for an order
     *
     * @param Order $order
     * @return string|null
     */
    public static function getShippingLabelPath(Order $order): ?string
    {
        $files = Storage::disk('shipping')->files();
        
        foreach ($files as $file) {
            if (str_starts_with($file, "shipping-label-{$order->id}-")) {
                return $file;
            }
        }
        
        return null;
    }

    /**
     * Check if shipping label exists for order
     *
     * @param Order $order
     * @return bool
     */
    public static function exists(Order $order): bool
    {
        return self::getShippingLabelPath($order) !== null;
    }

    /**
     * Download shipping label
     *
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public static function download(Order $order)
    {
        $path = self::getShippingLabelPath($order);
        
        if (!$path) {
            return null;
        }
        
        return FileStorageService::download($path, 'shipping', "shipping-label-{$order->id}.html");
    }

    /**
     * Generate shipping label HTML content
     *
     * @param Order $order
     * @return string
     */
    protected static function generateShippingLabelHtml(Order $order): string
    {
        $trackingNumber = 'TRK' . str_pad($order->id, 10, '0', STR_PAD_LEFT);
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shipping Label - Order #{$order->id}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 14px;
        }
        .label-container {
            border: 2px solid #000;
            padding: 20px;
            width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .tracking {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 20px 0;
            padding: 15px;
            border: 3px solid #000;
        }
        .barcode {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 36px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <h1>SHIPPING LABEL</h1>
            <p>Order ID: #{$order->id}</p>
        </div>

        <div class="section">
            <div class="section-title">SHIP TO:</div>
            <strong>{$order->full_name}</strong><br>
            {$order->address}<br>
            Phone: {$order->phone}<br>
            Email: {$order->email}
        </div>

        <div class="section">
            <div class="section-title">SHIP FROM:</div>
            <strong>E-Commerce Platform</strong><br>
            Vendor ID: {$order->vendor_id}<br>
            Warehouse Location
        </div>

        <div class="tracking">
            TRACKING NUMBER<br>
            {$trackingNumber}
        </div>

        <div class="barcode">
            ||||| |||| ||| |||| |||||
        </div>

        <div class="section">
            <div class="section-title">ORDER DETAILS:</div>
            Product: {$order->product->name}<br>
            Quantity: {$order->quantity}<br>
            Weight: N/A<br>
            Dimensions: N/A<br>
            Service: Standard Shipping
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
            <p>Generated: {$order->created_at->format('Y-m-d H:i:s')}</p>
            <p>Handle with care. Fragile items may be inside.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Generate shipping label for order if not exists
     *
     * @param Order $order
     * @return string
     */
    public static function generateIfNotExists(Order $order): string
    {
        $path = self::getShippingLabelPath($order);
        
        if ($path) {
            return $path;
        }
        
        return self::generate($order);
    }

    /**
     * Generate tracking number for order
     *
     * @param Order $order
     * @return string
     */
    public static function generateTrackingNumber(Order $order): string
    {
        return 'TRK' . str_pad($order->id, 10, '0', STR_PAD_LEFT);
    }
}
