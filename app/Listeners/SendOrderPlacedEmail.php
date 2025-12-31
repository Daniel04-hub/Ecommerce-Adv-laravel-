<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderConfirmationEmailJob;

class SendOrderPlacedEmail
{
    public function handle(OrderPlaced $event): void
    {
        // Send a customer-facing email when an order is placed.
        // Job is idempotent via cache and runs on the payment queue.
        SendOrderConfirmationEmailJob::dispatch($event->orderId);
    }
}
