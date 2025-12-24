<?php

namespace App\Jobs;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendShippingUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(Order $order)
    {
        $this->orderId = $order->id;
    }

    public function handle(): void
    {
        $cacheKey = "shipping_update_sent_{$this->orderId}";
        if (Cache::has($cacheKey)) {
            Log::info('Shipping update email already sent (idempotent skip)', [
                'order_id' => $this->orderId,
            ]);
            return;
        }

        $order = Order::with('user')->find($this->orderId);
        if (!$order || !$order->user) {
            Log::warning('Shipping update email skipped: order or user missing', [
                'order_id' => $this->orderId,
            ]);
            return;
        }

        // Do not resend if status is no longer shipped
        if ($order->status !== 'shipped') {
            Log::info('Shipping update email skipped: status not shipped anymore', [
                'order_id' => $this->orderId,
                'status' => $order->status,
            ]);
            return;
        }

        $data = [
            'orderId' => $order->id,
            'userName' => $order->user->name ?? 'Customer',
            'status' => 'shipped',
        ];

        // Mark as processed for 24h (prevents duplicates)
        Cache::put($cacheKey, true, 86400);

        Log::info('Subject: Your order has been shipped', [
            'order_id' => $this->orderId,
        ]);

        Mail::to($order->user->email)->send(new OrderShippedMail($data));
    }
}
