<?php

namespace App\Jobs;

use App\Mail\ShippingUpdateMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendShippingUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public ?string $status;

    public function __construct(int $orderId, ?string $status = null)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    public function handle(): void
    {
        $order = Order::with('user')->find($this->orderId);

        if (!$order || !$order->user) {
            Log::warning('Shipping update email skipped: order or user missing', [
                'order_id' => $this->orderId,
            ]);
            return;
        }

        $data = [
            'orderId' => $order->id,
            'userName' => $order->user->name ?? 'Customer',
            'status' => $this->status ?? ($order->status ?? 'shipped'),
        ];

        Mail::to($order->user->email)->send(new ShippingUpdateMail($data));
    }
}
