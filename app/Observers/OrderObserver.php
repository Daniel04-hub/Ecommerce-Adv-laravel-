<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Intentionally no mail dispatch here.
        // Order confirmation is handled via the OrderPlaced event → listener → job flow.
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Do not auto-dispatch status events here.
        // Status transitions and their events are handled explicitly by vendor actions.
    }
}
