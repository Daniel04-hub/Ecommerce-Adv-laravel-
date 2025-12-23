<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;



class VerifyPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue(config('queues.payment'));
    }


    public function handle(): void
    {
        Log::info('VerifyPaymentJob EXECUTED', [
            'order_id' => $this->orderId,
        ]);

        $order = \App\Models\Order::find($this->orderId);

        if (!$order) {
            return;
        }

        $order->update(['status' => 'completed']);
    }
}