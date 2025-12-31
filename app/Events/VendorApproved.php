<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $vendorId,
    ) {
    }
}
