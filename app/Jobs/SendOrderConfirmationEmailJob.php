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

    public int $tries = 5;

    /**
     * Backoff in seconds between attempts.
     * Helps with SMTP transient failures / Mailtrap rate limiting.
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60, 120];

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue(config('queues.payment'));
    }

    public function handle(): void
    {
        try {
            // Idempotency check: prevent duplicate email if job runs twice
            $cacheKey = "order_confirmation_sent_{$this->orderId}";
            if (Cache::has($cacheKey)) {
                Log::debug('Order confirmation email already sent (idempotent skip)', [
                    'order_id' => $this->orderId,
                ]);
                return;
            }

            // Prevent concurrent duplicate sends
            $processingKey = $cacheKey . '_processing';
            if (! Cache::add($processingKey, true, 60)) {
                Log::debug('Order confirmation email already being processed (concurrent skip)', [
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

            // Queue mail (do not send synchronously)
            $mailable = (new OrderPlacedMail($data))
                ->onQueue(config('queues.payment'));
            Mail::to($order->user->email)->queue($mailable);

            Log::info('Email queued: OrderPlacedMail', [
                'order_id' => $this->orderId,
                'to' => $order->user->email,
            ]);

            // Mark as processed (24-hour TTL) AFTER a successful send
            Cache::put($cacheKey, true, 86400);
        } catch (\Exception $e) {
            Log::error('Order confirmation email failed', [
                'order_id' => $this->orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            if (isset($processingKey)) {
                Cache::forget($processingKey);
            }
        }
    }
}
