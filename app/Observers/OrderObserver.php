<?php

namespace App\Observers;

use App\Jobs\SendShippingUpdateJob;
use App\Jobs\SendOrderConfirmationEmailJob;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Idempotent job handles duplicate protection
        SendOrderConfirmationEmailJob::dispatch($order->id)->onQueue(config('queues.shipping'));
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $previous = $order->getOriginal('status');
        $current = $order->status;

        // Only dispatch when status transitioned to shipped (deterministic)
        if ($previous !== 'shipped' && $current === 'shipped' && $order->wasChanged('status')) {
            SendShippingUpdateJob::dispatch($order)->onQueue(config('queues.shipping'));
        }
    }
}
