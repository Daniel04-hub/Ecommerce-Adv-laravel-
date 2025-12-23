<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\VerifyPaymentJob;
use Illuminate\Support\Facades\Log;

class StartPaymentFlow
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('StartPaymentFlow LISTENER HIT', [
            'order_id' => $event->orderId,
        ]);

        VerifyPaymentJob::dispatch($event->orderId)
            ->onQueue(config('queues.payment'));
    }
}
