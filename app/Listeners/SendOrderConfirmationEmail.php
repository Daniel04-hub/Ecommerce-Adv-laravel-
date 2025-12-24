<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderConfirmationEmailJob;

class SendOrderConfirmationEmail
{
    public function handle(OrderPlaced $event): void
    {
        // Dispatch ONE job (idempotency handled by job via cache)
        SendOrderConfirmationEmailJob::dispatch($event->orderId);
    }
}
