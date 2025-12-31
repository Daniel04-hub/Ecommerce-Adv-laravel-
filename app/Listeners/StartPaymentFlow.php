<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\VerifyPaymentJob;

class StartPaymentFlow
{
    public function handle(OrderStatusUpdated $event): void
    {
        if ($event->newStatus !== 'accepted') {
            return;
        }

        VerifyPaymentJob::dispatch($event->order->id)
            ->onQueue(config('queues.payment'));
    }
}
