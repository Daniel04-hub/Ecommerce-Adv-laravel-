<?php

namespace App\Listeners;

use App\Events\VendorApproved;
use App\Jobs\SendVendorApprovedEmailJob;

class DispatchVendorApprovedEmail
{
    public function handle(VendorApproved $event): void
    {
        SendVendorApprovedEmailJob::dispatch($event->vendorId);
    }
}
