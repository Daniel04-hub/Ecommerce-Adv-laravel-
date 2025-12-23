<?php

namespace App\Listeners;

use App\Events\StockUpdated;
use App\Jobs\PrepareShipmentJob;

class DispatchPrepareShipment
{
    public function handle(StockUpdated $event): void
    {
        PrepareShipmentJob::dispatch($event->orderId)
            ->onQueue(config('queues.shipping'));
    }
}
