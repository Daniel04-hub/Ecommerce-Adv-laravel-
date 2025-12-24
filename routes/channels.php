<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channels.
|
*/

// Customer can listen to their own orders
Broadcast::channel('orders.customer.{userId}', function ($user, $userId) {
    return (int)$user->id === (int)$userId;
});

// Vendor can listen to their orders
Broadcast::channel('orders.vendor.{vendorId}', function ($user, $vendorId) {
    $isVendor = (method_exists($user, 'hasRole') ? $user->hasRole('vendor') : (($user->role ?? null) === 'vendor'));
    if (! $isVendor) {
        return false;
    }

    return (int)($user->vendor_id ?? 0) === (int)$vendorId;
});

// Presence channel example for vendor dashboard (multiple vendors watching same order)
Broadcast::channel('vendor.orders.{orderId}', function ($user, $orderId) {
    if (!$user->hasRole('vendor')) {
        return false;
    }

    $order = Order::find($orderId);
    if (!$order) {
        return false;
    }

    // Only vendor assigned to this order can see it
    return (int)$order->vendor_id === (int)($user->vendor_id ?? 0);
});
