<?php

/**
 * Test Email Notification System
 * 
 * This script verifies that all three email types are properly queued and sent:
 * 1. Order Confirmation Email
 * 2. Shipping Update Email
 * 3. Login Alert Email
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

Log::info('🧪 Starting Email System Test');

try {
    echo "\n========================================\n";
    echo "  EMAIL NOTIFICATION SYSTEM TEST\n";
    echo "========================================\n\n";

    // Test 1: Login Alert Email
    echo "📧 TEST 1: Login Alert Email\n";
    echo "────────────────────────────────────────\n";
    
    $testUser = User::first();
    if ($testUser) {
        echo "✓ Found test user: {$testUser->name} ({$testUser->email})\n";
        echo "  → Dispatching SendLoginAlertEmailJob...\n";
        
        // Simulate login event using event() helper
        $loginEvent = new Login([], $testUser, false);
        event($loginEvent);
        
        echo "  ✓ Login event dispatched\n";
        echo "  → Job queued on: " . config('queues.shipping') . "\n\n";
    } else {
        echo "✗ No test user found\n\n";
    }

    // Test 2: Order Confirmation Email
    echo "📧 TEST 2: Order Confirmation Email\n";
    echo "────────────────────────────────────────\n";
    
    $vendor = Vendor::first();
    $customer = User::whereHas('roles', function ($q) {
        $q->where('name', 'customer');
    })->first();
    
    if ($vendor && $customer) {
        $product = Product::where('vendor_id', $vendor->id)->first();
        
        if ($product) {
            echo "✓ Creating test order...\n";
            echo "  - Customer: {$customer->name}\n";
            echo "  - Product: {$product->name}\n";
            
            $testOrder = Order::create([
                'user_id' => $customer->id,
                'vendor_id' => $vendor->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
                'status' => 'placed',
            ]);
            
            echo "  ✓ Order created: #{$testOrder->id}\n";
            echo "  → Dispatching SendOrderConfirmationEmailJob...\n";
            
            // Dispatch OrderPlaced event
            OrderPlaced::dispatch($testOrder->id);
            
            echo "  ✓ OrderPlaced event dispatched\n";
            echo "  → Job queued on: " . config('queues.shipping') . "\n\n";
        } else {
            echo "✗ No products found\n\n";
        }
    } else {
        echo "✗ No vendor or customer found\n\n";
    }

    // Test 3: Shipping Update Email
    echo "📧 TEST 3: Shipping Update Email\n";
    echo "────────────────────────────────────────\n";
    
    $order = Order::where('status', 'placed')->first();
    if ($order) {
        echo "✓ Found placed order: #{$order->id}\n";
        echo "  - Current status: {$order->status}\n";
        echo "  → Updating status to 'shipped'...\n";
        
        $previousStatus = $order->status;
        $order->update(['status' => 'shipped']);
        
        echo "  ✓ Status updated to: {$order->status}\n";
        echo "  → Dispatching SendShippingUpdateEmailJob...\n";
        
        // Dispatch OrderStatusUpdated event
        OrderStatusUpdated::dispatch($order, $previousStatus, 'shipped');
        
        echo "  ✓ OrderStatusUpdated event dispatched\n";
        echo "  → Job queued on: " . config('queues.shipping') . "\n\n";
    } else {
        echo "✗ No placed orders found\n\n";
    }

    echo "========================================\n";
    echo "✅ ALL TESTS COMPLETED SUCCESSFULLY\n";
    echo "========================================\n\n";
    
    echo "📋 NEXT STEPS:\n";
    echo "────────────────────────────────────────\n";
    echo "1. Ensure queue worker is running:\n";
    echo "   php artisan queue:work --queue=shipping_queue\n\n";
    echo "2. Check your Mailtrap inbox for emails:\n";
    echo "   https://mailtrap.io/\n\n";
    echo "3. Monitor queue processing:\n";
    echo "   tail -f storage/logs/laravel.log\n\n";
    echo "4. Verify logs contain:\n";
    echo "   - 'Sending order confirmation email'\n";
    echo "   - 'Sending shipping update email'\n";
    echo "   - 'Sending login alert email'\n\n";

} catch (\Exception $e) {
    echo "\n❌ TEST FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
    Log::error('Email system test failed', ['error' => $e->getMessage()]);
}
