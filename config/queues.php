<?php

return [
    'payment' => env('QUEUE_PAYMENT', 'payment_queue'),
    'inventory' => env('QUEUE_INVENTORY', 'inventory_queue'),
    'shipping' => env('QUEUE_SHIPPING', 'shipping_queue'),

    // Legacy keys retained for compatibility with existing references.
    'payment_queue' => env('QUEUE_PAYMENT', 'payment_queue'),
    'inventory_queue' => env('QUEUE_INVENTORY', 'inventory_queue'),
    'shipping_queue' => env('QUEUE_SHIPPING', 'shipping_queue'),
];
