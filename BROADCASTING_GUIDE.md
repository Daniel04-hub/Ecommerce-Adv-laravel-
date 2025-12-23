# STEP 9 — BROADCASTING (LIVE ORDER STATUS) — IMPLEMENTATION GUIDE

## Overview
Real-time order status updates using Laravel Reverb with private channels for secure broadcasting.

## Files Created/Modified

### 1. **config/broadcasting.php** (NEW)
- Broadcasting configuration
- Supports Reverb, Pusher, Ably, and null driver
- Default: `reverb` (self-hosted, no external APIs)

### 2. **app/Events/OrderStatusUpdated.php** (NEW)
Broadcasts order status changes to:
- Customer's private channel: `orders.customer.{user_id}`
- Vendor's private channel: `orders.vendor.{vendor_id}`

**Event Data:**
```php
{
    'id': Order ID,
    'product': Product name,
    'quantity': Order quantity,
    'price': Total price,
    'status': New status (placed/accepted/shipped/completed),
    'previous_status': Previous status,
    'updated_at': ISO timestamp
}
```

### 3. **app/Services/OrderStatusService.php** (NEW)
Helper service for updating order status with automatic broadcasting.

**Usage:**
```php
use App\Services\OrderStatusService;

// Update status and broadcast
OrderStatusService::update($order, 'shipped');

// Get status label with icon
$info = OrderStatusService::getStatusLabel('shipped');
// Returns: ['label' => 'Shipped', 'icon' => 'truck', 'color' => 'primary']
```

### 4. **routes/channels.php** (NEW)
Authorization channels for private broadcasts.

**Available Channels:**
- `orders.customer.{userId}` — Customer watches their orders
- `orders.vendor.{vendorId}` — Vendor watches their orders
- `vendor.orders.{orderId}` — Vendor presence channel (optional)

### 5. **resources/views/components/order-status-listener.blade.php** (NEW)
Blade component for real-time listening in views.

**Usage in any view:**
```blade
@include('components.order-status-listener', [
    'orderId' => $order->id,
    'userId' => auth()->id(),
    'isVendor' => auth()->user()->hasRole('vendor')
])
```

**Features:**
- Connects to Laravel Echo
- Listens for `order.status-updated` events
- Shows toast notifications
- Updates order status badge in real-time
- Updates timeline visualization

### 6. **resources/views/customer/orders/show.blade.php** (NEW)
Order detail page with real-time status tracking.

**Features:**
- Interactive timeline showing order progress
- Live status badge (updates in real-time)
- Product information
- Order summary with pricing
- Delivery address
- Automatic real-time listener included

### 7. **routes/web.php** (MODIFIED)
Added new route:
```php
Route::get('/customer/orders/{order}', [OrderController::class, 'show'])
    ->middleware('role:customer')
    ->name('customer.orders.show');
```

### 8. **app/Http/Controllers/Customer/OrderController.php** (MODIFIED)
Added `show()` method to display individual order with broadcasting listener.

## How to Use

### Step 1: Update Order Status with Broadcasting

In any controller or service:

```php
use App\Services\OrderStatusService;
use App\Models\Order;

$order = Order::find($orderId);

// This will:
// 1. Validate transition (placed → accepted → shipped → completed)
// 2. Update order status
// 3. Broadcast OrderStatusUpdated event to private channels
OrderStatusService::update($order, 'shipped');
```

### Step 2: Enable Real-Time Updates in Frontend

Include the listener in any Blade template:

```blade
@include('components.order-status-listener', [
    'orderId' => $order->id,
    'userId' => auth()->id(),
    'isVendor' => auth()->user()->hasRole('vendor')
])
```

### Step 3: View Order with Real-Time Status

Navigate to:
```
/customer/orders/{order_id}
```

The page will:
- Display current order status
- Show interactive timeline
- Display toast notifications when status changes
- Update all UI elements in real-time

## Environment Setup

Add to `.env`:

```env
# Use Reverb (default)
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_APP_ID=1
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_USE_TLS=false

# Or use Pusher
# BROADCAST_DRIVER=pusher
# PUSHER_APP_KEY=
# PUSHER_APP_SECRET=
# PUSHER_APP_ID=
# PUSHER_APP_CLUSTER=
```

## Running Reverb Server (Optional)

When you're ready to test:

```bash
php artisan reverb:start
# Runs on localhost:8080
```

**Note:** Do NOT auto-start servers. User must explicitly run this command.

## Broadcasting Flow Diagram

```
1. Order Status Updated
   ↓
2. OrderStatusService::update($order, 'new_status')
   ↓
3. OrderStatusUpdated event dispatched
   ↓
4. Laravel broadcasts to private channels:
   - orders.customer.{user_id}
   - orders.vendor.{vendor_id}
   ↓
5. JavaScript listener (Laravel Echo) receives event
   ↓
6. Frontend updates UI:
   - Toast notification
   - Status badge
   - Timeline progress
```

## Security

✅ **Private Channels** — Only authenticated users receive events
✅ **Authorization** — Channel authorization checked via `routes/channels.php`
✅ **User Isolation** — Customer only sees their orders, vendor only sees their orders
✅ **Automatic Verification** — Laravel verifies CSRF token before connecting

## Existing Functionality Preserved

❌ **NO changes** to:
- Order models
- Order controllers (only added `show()` method)
- Order status validation logic
- Job dispatch flow
- Authentication system

## Example: Updating Order Status from Vendor Dashboard

```php
// In VendorOrderController or similar
use App\Services\OrderStatusService;

$order = Order::find($orderId);
OrderStatusService::update($order, 'shipped');

return redirect()->back()->with('success', 'Order shipped!');
// Automatically broadcasts to customer + vendor
```

## Testing Real-Time Updates

1. Start Reverb: `php artisan reverb:start`
2. Open two browser windows (customer + vendor)
3. Navigate to order detail page in both
4. Update status from vendor side
5. Watch customer side update in real-time ✨

## Next Steps

- Confirm STEP 9 is complete
- Ready for STEP 10 (File Storage System)
