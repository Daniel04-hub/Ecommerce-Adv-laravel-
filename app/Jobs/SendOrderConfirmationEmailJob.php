<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $order = Order::with('user')->find($this->orderId);

        if (!$order || !$order->user) {
            Log::warning('Order confirmation email skipped: order or user missing', [
                'order_id' => $this->orderId,
            ]);
            return;
        }

        $data = [
            'orderId' => $order->id,
            'userName' => $order->user->name ?? 'Customer',
            'total' => $order->total ?? null,
        ];

        Mail::to($order->user->email)->send(new OrderConfirmationMail($data));
    }
}
