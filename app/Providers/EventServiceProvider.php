<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PaymentSuccess;
use App\Events\VendorApproved;
use App\Events\VendorSuspended;
use App\Events\ProductApproved;
use App\Events\ProductRejected;
use App\Events\OrderStatusUpdated;
use App\Events\OrderPlaced;
use Illuminate\Auth\Events\Login;
use App\Listeners\StartPaymentFlow;
use App\Listeners\DispatchUpdateStock;
use App\Listeners\DispatchPrepareShipment;
use App\Listeners\SendShippingUpdateEmail;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendOrderPlacedEmail;
use App\Listeners\SendLoginAlertEmail;
use App\Listeners\DispatchVendorApprovedEmail;
use App\Listeners\DispatchVendorSuspendedEmail;
use App\Listeners\DispatchProductApprovedEmail;
use App\Listeners\DispatchProductRejectedEmail;

class EventServiceProvider extends ServiceProvider
{
    // Event → Job architecture locked on 2025-12-24
    // One action → One event → One listener → One job
    protected $listen = [
        // Customer places order: send customer confirmation email
        OrderPlaced::class => [
            SendOrderPlacedEmail::class,
        ],
        // Payment success: update inventory stock
        PaymentSuccess::class => [
            DispatchUpdateStock::class,
        ],
        // Vendor actions: start payment/stock/shipping/email flow based on status
        OrderStatusUpdated::class => [
            StartPaymentFlow::class,
            SendOrderConfirmationEmail::class,
            DispatchPrepareShipment::class,
            SendShippingUpdateEmail::class,
        ],
        // User login: send security alert email
        Login::class => [
            SendLoginAlertEmail::class,
        ],

        // Admin actions: vendor approval / suspension
        VendorApproved::class => [
            DispatchVendorApprovedEmail::class,
        ],
        VendorSuspended::class => [
            DispatchVendorSuspendedEmail::class,
        ],

        // Admin actions: product approval / rejection
        ProductApproved::class => [
            DispatchProductApprovedEmail::class,
        ],
        ProductRejected::class => [
            DispatchProductRejectedEmail::class,
        ],
    ];
}
