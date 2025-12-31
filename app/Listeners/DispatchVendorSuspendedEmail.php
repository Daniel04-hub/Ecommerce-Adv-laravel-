<?php

namespace App\Listeners;

use App\Events\VendorSuspended;
use App\Jobs\SendVendorSuspendedEmailJob;

class DispatchVendorSuspendedEmail
{
    public function handle(VendorSuspended $event): void
    {
        SendVendorSuspendedEmailJob::dispatch($event->vendorId);
    }
}
