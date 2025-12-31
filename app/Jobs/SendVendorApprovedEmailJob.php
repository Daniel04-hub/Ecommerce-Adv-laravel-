<?php

namespace App\Jobs;

use App\Mail\VendorApprovedMail;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVendorApprovedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @var array<int> */
    public array $backoff = [10, 30, 60, 120];

    public function __construct(
        public readonly int $vendorId,
    ) {
        $this->onQueue(config('queues.inventory'));
    }

    public function handle(): void
    {
        $vendor = Vendor::with('user')->find($this->vendorId);
        if (! $vendor) {
            Log::warning('SendVendorApprovedEmailJob: vendor not found', ['vendor_id' => $this->vendorId]);
            return;
        }

        $status = (string) ($vendor->status ?? '');
        $changedAt = $vendor->updated_at?->getTimestamp() ?? 0;
        $sentKey = "vendor_status_email_sent_{$vendor->id}_{$status}_{$changedAt}";
        if (\Illuminate\Support\Facades\Cache::has($sentKey)) {
            Log::debug('SendVendorApprovedEmailJob: already sent (idempotent skip)', ['vendor_id' => $vendor->id]);
            return;
        }

        $processingKey = $sentKey . '_processing';
        if (! \Illuminate\Support\Facades\Cache::add($processingKey, true, 60)) {
            Log::debug('SendVendorApprovedEmailJob: already processing (concurrent skip)', ['vendor_id' => $vendor->id]);
            return;
        }

        $email = $vendor->user?->email;
        if (! $email) {
            Log::warning('SendVendorApprovedEmailJob: vendor user email missing', ['vendor_id' => $vendor->id]);
            return;
        }

        try {
            $mailable = (new VendorApprovedMail([
                'vendor' => $vendor,
                'user' => $vendor->user,
            ]))->onQueue(config('queues.inventory'));
            Mail::to($email)->queue($mailable);

            Log::info('Email queued: VendorApprovedMail', [
                'vendor_id' => $vendor->id,
                'to' => $email,
            ]);

            \Illuminate\Support\Facades\Cache::put($sentKey, true, 86400);
        } finally {
            \Illuminate\Support\Facades\Cache::forget($processingKey);
        }
    }
}
