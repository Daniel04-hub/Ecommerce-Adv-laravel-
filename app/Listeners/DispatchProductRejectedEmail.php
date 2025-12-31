<?php

namespace App\Listeners;

use App\Events\ProductRejected;
use App\Jobs\SendProductRejectedEmailJob;

class DispatchProductRejectedEmail
{
    public function handle(ProductRejected $event): void
    {
        SendProductRejectedEmailJob::dispatch($event->productId);
    }
}
