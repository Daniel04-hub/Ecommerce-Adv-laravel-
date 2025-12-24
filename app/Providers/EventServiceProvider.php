<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderPlaced;
use App\Events\PaymentSuccess;
use App\Events\StockUpdated;
use App\Events\OrderStatusUpdated;
use Illuminate\Auth\Events\Login;
use App\Listeners\StartPaymentFlow;
use App\Listeners\DispatchUpdateStock;
use App\Listeners\DispatchPrepareShipment;
use App\Listeners\SendShippingUpdateEmail;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendLoginAlertEmail;

class EventServiceProvider extends ServiceProvider
{
    // Event → Job architecture locked on 2025-12-24
    // One action → One event → One listener → One job
    protected $listen = [
        // Order placed: verify payment + send confirmation email
        OrderPlaced::class => [
            StartPaymentFlow::class,
            SendOrderConfirmationEmail::class,
        ],
        // Payment success: update inventory stock
        PaymentSuccess::class => [
            DispatchUpdateStock::class,
        ],
        // Stock updated: prepare shipment
        StockUpdated::class => [
            DispatchPrepareShipment::class,
        ],
        // Order status updated: send shipping email (if status === 'shipped')
        OrderStatusUpdated::class => [
            SendShippingUpdateEmail::class,
        ],
        // User login: send security alert email
        Login::class => [
            SendLoginAlertEmail::class,
        ],
    ];
}
