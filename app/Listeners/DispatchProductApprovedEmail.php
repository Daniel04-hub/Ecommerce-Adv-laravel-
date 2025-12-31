<?php

namespace App\Listeners;

use App\Events\ProductApproved;
use App\Jobs\SendProductApprovedEmailJob;

class DispatchProductApprovedEmail
{
    public function handle(ProductApproved $event): void
    {
        SendProductApprovedEmailJob::dispatch($event->productId);
    }
}
