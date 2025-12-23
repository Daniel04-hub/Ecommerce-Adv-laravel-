<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderPlaced;
use App\Events\PaymentSuccess;
use App\Events\StockUpdated;
use App\Listeners\DispatchPrepareShipment;
use App\Listeners\DispatchUpdateStock;
use App\Listeners\StartPaymentFlow;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            StartPaymentFlow::class,
        ],
        PaymentSuccess::class => [
            DispatchUpdateStock::class,
        ],
        StockUpdated::class => [
            DispatchPrepareShipment::class,
        ],
    ];
}
