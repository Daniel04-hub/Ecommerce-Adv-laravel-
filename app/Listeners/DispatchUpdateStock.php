<?php

namespace App\Listeners;

use App\Events\PaymentSuccess;
use App\Jobs\UpdateStockJob;

class DispatchUpdateStock
{
    public function handle(PaymentSuccess $event): void
    {
        UpdateStockJob::dispatch($event->orderId)
            ->onQueue(config('queues.inventory'));
    }
}
