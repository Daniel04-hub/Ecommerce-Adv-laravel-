<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\PrepareShipmentJob;

class DispatchPrepareShipment
{
    public function handle(OrderStatusUpdated $event): void
    {
        if ($event->newStatus !== 'shipped') {
            return;
        }

        PrepareShipmentJob::dispatch($event->order->id)
            ->onQueue(config('queues.shipping'));
    }
}
