<?php

namespace App\Jobs;

use App\Mail\ProductApprovedMail;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProductApprovedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @var array<int> */
    public array $backoff = [10, 30, 60, 120];

    public function __construct(
        public readonly int $productId,
    ) {
        $this->onQueue(config('queues.inventory'));
    }

    public function handle(): void
    {
        $product = Product::with('vendor.user')->find($this->productId);
        if (! $product) {
            Log::warning('SendProductApprovedEmailJob: product not found', ['product_id' => $this->productId]);
            return;
        }

        $status = (string) ($product->status ?? '');
        $changedAt = $product->updated_at?->getTimestamp() ?? 0;
        $sentKey = "product_status_email_sent_{$product->id}_{$status}_{$changedAt}";
        if (\Illuminate\Support\Facades\Cache::has($sentKey)) {
            Log::debug('SendProductApprovedEmailJob: already sent (idempotent skip)', ['product_id' => $product->id]);
            return;
        }

        $processingKey = $sentKey . '_processing';
        if (! \Illuminate\Support\Facades\Cache::add($processingKey, true, 60)) {
            Log::debug('SendProductApprovedEmailJob: already processing (concurrent skip)', ['product_id' => $product->id]);
            return;
        }

        $email = $product->vendor?->user?->email;
        if (! $email) {
            Log::warning('SendProductApprovedEmailJob: vendor user email missing', ['product_id' => $product->id]);
            return;
        }

        try {
            $mailable = (new ProductApprovedMail([
                'product' => $product,
                'vendor' => $product->vendor,
                'user' => $product->vendor?->user,
            ]))->onQueue(config('queues.inventory'));
            Mail::to($email)->queue($mailable);

            Log::info('Email queued: ProductApprovedMail', [
                'product_id' => $product->id,
                'to' => $email,
            ]);

            \Illuminate\Support\Facades\Cache::put($sentKey, true, 86400);
        } finally {
            \Illuminate\Support\Facades\Cache::forget($processingKey);
        }
    }
}
