<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\SendShippingUpdateEmailJob;

class SendShippingUpdateEmail
{
    public function handle(OrderStatusUpdated $event): void
    {
        if ($event->newStatus !== 'shipped') {
            return;
        }

        // Dispatch ONE job (idempotency handled by job via cache)
        SendShippingUpdateEmailJob::dispatch($event->order, $event->newStatus);
    }
}
