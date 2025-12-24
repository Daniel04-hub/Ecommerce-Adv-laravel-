<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue(config('queues.shipping'));
    }

    public function handle(): void
    {
        try {
            // Idempotency check: prevent duplicate email if job runs twice
            $cacheKey = "order_confirmation_sent_{$this->orderId}";
            if (Cache::has($cacheKey)) {
                Log::info('Order confirmation email already sent (idempotent skip)', [
                    'order_id' => $this->orderId,
                ]);
                return;
            }

            $order = Order::with('user')->find($this->orderId);

            if (!$order || !$order->user) {
                Log::warning('Order confirmation email skipped: order or user missing', [
                    'order_id' => $this->orderId,
                ]);
                return;
            }

            // Build validated data array
            $data = [
                'orderId' => $order->id,
                'userName' => $order->user->name ?? 'Customer',
                'userEmail' => $order->user->email,
                'orderDate' => $order->created_at?->toDateString() ?? now()->toDateString(),
                'total' => $order->price * $order->quantity,
                'status' => $order->status,
            ];

            // Guard: ensure all required keys exist
            if (!isset($data['orderId']) || !isset($data['userName']) || !isset($data['userEmail'])) {
                Log::error('Order confirmation email skipped: required data missing', [
                    'order_id' => $this->orderId,
                    'data_keys' => array_keys($data),
                ]);
                return;
            }

            Log::info('Sending order confirmation email', [
                'order_id' => $this->orderId,
                'user_id' => $order->user->id,
            ]);

            // Mark as processed (24-hour TTL)
            Cache::put($cacheKey, true, 86400);

            // ONLY place where mail is sent
            Mail::to($order->user->email)->send(new OrderPlacedMail($data));

            Log::info('Order confirmation email sent successfully', [
                'order_id' => $this->orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Order confirmation email failed', [
                'order_id' => $this->orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
