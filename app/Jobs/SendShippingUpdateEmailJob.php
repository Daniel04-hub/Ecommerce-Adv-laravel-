<?php

namespace App\Jobs;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendShippingUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public ?string $status;

    public function __construct(Order $order, ?string $status = null)
    {
        $this->orderId = $order->id;
        $this->status = $status;
        $this->onQueue(config('queues.shipping'));
    }

    public function handle(): void
    {
        try {
            // Idempotency check: prevent duplicate email if job runs twice
            $cacheKey = "shipping_update_sent_{$this->orderId}";
            if (Cache::has($cacheKey)) {
                Log::debug('Shipping update email already sent (idempotent skip)', [
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

            // Build validated data array
            $data = [
                'orderId' => $order->id,
                'userName' => $order->user->name ?? 'Customer',
                'userEmail' => $order->user->email,
                'status' => $this->status ?? ($order->status ?? 'shipped'),
            ];

            // Guard: ensure all required keys exist
            if (!isset($data['orderId']) || !isset($data['userName']) || !isset($data['userEmail'])) {
                Log::error('Shipping update email skipped: required data missing', [
                    'order_id' => $this->orderId,
                    'data_keys' => array_keys($data),
                ]);
                return;
            }

            // Mark as processed (24-hour TTL)
            Cache::put($cacheKey, true, 86400);

            // Queue mail (do not send synchronously)
            $mailable = (new OrderShippedMail($data))
                ->onQueue(config('queues.shipping'));
            Mail::to($order->user->email)->queue($mailable);

            Log::info('Email queued: OrderShippedMail', [
                'order_id' => $this->orderId,
                'to' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Shipping update email failed', [
                'order_id' => $this->orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
