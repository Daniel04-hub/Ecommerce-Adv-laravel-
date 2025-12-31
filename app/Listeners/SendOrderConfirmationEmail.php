<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\SendOrderConfirmationEmailJob;

class SendOrderConfirmationEmail
{
    public function handle(OrderStatusUpdated $event): void
    {
        if ($event->newStatus !== 'accepted') {
            return;
        }

        // Dispatch ONE job (idempotency handled by job via cache)
        SendOrderConfirmationEmailJob::dispatch($event->order->id);
    }
}
