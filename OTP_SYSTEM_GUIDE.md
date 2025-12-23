# STEP 12 — OTP SYSTEM — IMPLEMENTATION GUIDE

## Overview
Secure One-Time Password system for passwordless login and COD order verification with email/SMS delivery.

## Key Features

### 1. **Passwordless Login with OTP**
- Alternative to traditional password-based login
- 6-digit OTP sent via email
- 5-minute expiry
- Max 3 verification attempts
- Preserves existing login flow

### 2. **COD Verification OTP**
- Secure Cash-on-Delivery verification
- Customer receives OTP for their order
- Delivery person verifies OTP before collecting payment
- 15-minute expiry
- Prevents fraudulent deliveries

### 3. **Mock SMS System**
- SMS-ready infrastructure (mock implementation)
- Easy integration with real SMS gateway (Twilio, Nexmo, etc.)
- Logs SMS to file for testing

## Files Created

### 1. **app/Services/OtpService.php** (NEW)
Universal OTP generation and verification service.

**Key Methods:**
```php
// Generate 6-digit OTP
OtpService::generateCode(); // Returns: "123456"

// Generate and store OTP with purpose
OtpService::generate($identifier, $purpose, $expiryMinutes);
// $purpose: 'login', 'cod_verification', 'general'

// Verify OTP code
OtpService::verify($identifier, $code, $purpose, $deleteAfterVerify);
// Returns: true/false
// Max 3 attempts before invalidation

// Check if OTP exists
OtpService::exists($identifier, $purpose);

// Get remaining time
OtpService::getRemainingTime($identifier, $purpose); // Returns seconds

// Delete OTP
OtpService::delete($identifier, $purpose);

// Send via email
OtpService::sendViaEmail($email, $code, $purpose);

// Send via SMS (mock)
OtpService::sendViaSms($phone, $code);

// Get OTP info without revealing code
OtpService::getInfo($identifier, $purpose);
```

**Storage:**
- Uses Laravel Cache (no database)
- Automatic expiry
- Secure hashed keys
- Tracks verification attempts

**Expiry Times:**
- Login: 5 minutes (`OtpService::LOGIN_EXPIRY`)
- COD Verification: 15 minutes (`OtpService::COD_EXPIRY`)
- General: 10 minutes (`OtpService::DEFAULT_EXPIRY`)

### 2. **app/Mail/OtpMail.php** (NEW)
Mailable for OTP delivery.

**Features:**
- Purpose-specific subject lines
- Auto-detects expiry time
- Professional HTML template
- Security warnings

**Usage:**
```php
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

Mail::to($email)->send(new OtpMail($code, 'login'));
```

### 3. **resources/views/emails/otp.blade.php** (NEW)
Professional OTP email template.

**Features:**
- Gradient design
- Large, readable OTP code
- Purpose badges
- Security warnings
- Mobile responsive

### 4. **app/Http/Controllers/Auth/OtpLoginController.php** (NEW)
Handles OTP-based authentication flow.

**Routes:**
```php
GET  /login/otp              → showRequestForm()   // Request OTP
POST /login/otp/send         → sendOtp()           // Send OTP to email
GET  /login/otp/verify       → showVerifyForm()    // Verification page
POST /login/otp/verify       → verifyOtp()         // Verify & login
POST /login/otp/resend       → resendOtp()         // Resend OTP
```

**Flow:**
1. User enters email
2. System sends 6-digit OTP
3. User enters OTP on verification page
4. System verifies OTP and logs user in
5. Redirects based on role (admin/vendor/customer)

**Spam Protection:**
- Prevents multiple OTP requests within expiry window
- Shows remaining time if OTP already sent

### 5. **app/Http/Controllers/CodVerificationController.php** (NEW)
COD order verification system.

**Routes:**
```php
POST /orders/{order}/cod/generate-otp → generateOtp()        // Customer generates OTP
GET  /orders/{order}/cod/status       → checkOtpStatus()     // Check OTP status
GET  /cod/verify                      → showVerificationForm() // Delivery form
POST /cod/verify                      → verifyOtp()          // Delivery verifies
```

**Flow:**
1. Customer generates OTP for their COD order
2. OTP sent to customer's email & phone (SMS mock)
3. Customer shares OTP with delivery person
4. Delivery person enters OTP on verification page
5. Order marked as delivered upon successful verification

**Security:**
- Customer can only generate OTP for their own orders
- OTP only works for COD orders
- Valid only for specific order statuses (processing, shipped, out_for_delivery)
- Auto-updates order status to 'delivered' after verification

### 6. **resources/views/auth/otp-request.blade.php** (NEW)
OTP login request page.

**Features:**
- Clean, modern UI
- Email input with validation
- Link to password-based login
- How-it-works guide

### 7. **resources/views/auth/otp-verify.blade.php** (NEW)
OTP verification page with countdown.

**Features:**
- Large OTP input field (auto-submits at 6 digits)
- Countdown timer (5 minutes)
- Resend OTP button
- Auto-validation
- Monospace font for code clarity

**JavaScript Features:**
- Real-time countdown display
- Auto-submit when 6 digits entered
- Resend OTP via AJAX
- Digit-only input validation

### 8. **resources/views/cod/verify.blade.php** (NEW)
COD verification form for delivery personnel.

**Features:**
- Order ID input
- OTP input
- Real-time verification
- Success/error alerts
- Step-by-step instructions

**Usage:**
- Public page (no authentication)
- Delivery person accesses via mobile
- Verifies order before collecting payment

## Files Modified

### 1. **routes/web.php** (MODIFIED)
Added OTP and COD verification routes.

```php
// OTP Login Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login/otp', [OtpLoginController::class, 'showRequestForm'])
        ->name('otp.request');
    Route::post('/login/otp/send', [OtpLoginController::class, 'sendOtp'])
        ->name('otp.send');
    Route::get('/login/otp/verify', [OtpLoginController::class, 'showVerifyForm'])
        ->name('otp.verify.form');
    Route::post('/login/otp/verify', [OtpLoginController::class, 'verifyOtp'])
        ->name('otp.verify');
    Route::post('/login/otp/resend', [OtpLoginController::class, 'resendOtp'])
        ->name('otp.resend');
});

// COD Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/orders/{order}/cod/generate-otp', [CodVerificationController::class, 'generateOtp'])
        ->name('cod.generate');
    Route::get('/orders/{order}/cod/status', [CodVerificationController::class, 'checkOtpStatus'])
        ->name('cod.status');
});

// Public COD verification (delivery person, no auth)
Route::get('/cod/verify', [CodVerificationController::class, 'showVerificationForm'])
    ->name('cod.verify.form');
Route::post('/cod/verify', [CodVerificationController::class, 'verifyOtp'])
    ->name('cod.verify');
```

### 2. **resources/views/auth/login.blade.php** (MODIFIED)
Added "Login with OTP" button.

**Changes:**
- New button below Google OAuth
- Links to OTP login flow
- Preserves existing login functionality
- No breaking changes

### 3. **resources/views/customer/orders/show.blade.php** (MODIFIED)
Added COD OTP generation button.

**Changes:**
- Shows "Generate COD Verification OTP" for eligible orders
- Only visible for COD orders in processing/shipped/out_for_delivery status
- Includes helpful instructions

## Usage Examples

### 1. Login with OTP

**User Flow:**
1. Visit login page
2. Click "Login with OTP (Passwordless)"
3. Enter email address
4. Check email for 6-digit code
5. Enter code on verification page
6. Automatically logged in

**Code:**
```php
// Generate and send OTP
$code = OtpService::generate($email, 'login', 5);
OtpService::sendViaEmail($email, $code, 'login');

// Verify OTP
$valid = OtpService::verify($email, $userEnteredCode, 'login');
if ($valid) {
    Auth::login($user);
}
```

### 2. COD Order Verification

**Customer:**
```php
// Generate OTP for order
$code = OtpService::generate("order-{$order->id}", 'cod_verification', 15);
OtpService::sendViaEmail($order->email, $code, 'cod_verification');
OtpService::sendViaSms($order->phone, $code); // Mock SMS
```

**Delivery Person:**
```php
// Verify OTP
$valid = OtpService::verify("order-{$order->id}", $deliveryPersonCode, 'cod_verification');
if ($valid) {
    $order->update(['status' => 'delivered']);
}
```

### 3. Check OTP Status (AJAX)

```javascript
fetch('/orders/' + orderId + '/cod/status')
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            console.log('Remaining time:', data.remaining_formatted);
            console.log('Attempts:', data.attempts);
        }
    });
```

### 4. Resend OTP

```javascript
fetch('/login/otp/resend', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ email: email })
})
.then(response => response.json())
.then(data => {
    alert(data.message);
});
```

## Security Features

### OTP Generation
✅ **Cryptographically Secure** - Uses `random_int()` for unpredictable codes
✅ **Time-Limited** - Automatic expiry after set duration
✅ **Single-Use** - Deleted after successful verification
✅ **Attempt Limiting** - Max 3 attempts before invalidation

### Storage Security
✅ **Hashed Keys** - SHA-256 hash of identifier
✅ **Cache-Based** - No database storage (ephemeral)
✅ **Automatic Cleanup** - Laravel cache handles expiry
✅ **No Code Exposure** - `getInfo()` never returns actual code

### Anti-Spam
✅ **Rate Limiting** - Prevents rapid OTP requests
✅ **Cooldown Period** - Shows remaining time
✅ **Email Verification** - Only sends to registered users

### COD Verification
✅ **Ownership Validation** - Customer must own the order
✅ **Order Type Check** - Only COD orders
✅ **Status Validation** - Appropriate order statuses only
✅ **One-Time Use** - OTP deleted after verification

## Testing

### Test OTP Login

```bash
# Start application
php artisan serve

# Visit OTP login
http://localhost:8000/login/otp

# Enter registered email
# Check terminal/logs for OTP (if queue not running)

# Or check email inbox
```

### Test COD Verification

```php
// In tinker
php artisan tinker

>>> $order = Order::where('payment_method', 'COD')->first();
>>> $code = \App\Services\OtpService::generate("order-{$order->id}", 'cod_verification', 15);
>>> echo "OTP: $code\n";
>>> \App\Services\OtpService::verify("order-{$order->id}", $code, 'cod_verification');
// Returns: true
```

### Test SMS Mock

```bash
# Check SMS log file
tail -f storage/logs/sms-mock.log

# You'll see:
# 2025-12-18 10:30:45 | +1234567890 | OTP: 123456
```

### Test OTP Service

```php
use App\Services\OtpService;

// Generate OTP
$code = OtpService::generate('test@example.com', 'login', 5);
echo "Code: $code\n";

// Check if exists
OtpService::exists('test@example.com', 'login'); // true

// Get remaining time
$remaining = OtpService::getRemainingTime('test@example.com', 'login');
echo "Remaining: {$remaining}s\n";

// Verify (wrong code)
OtpService::verify('test@example.com', '000000', 'login'); // false

// Verify (correct code)
OtpService::verify('test@example.com', $code, 'login'); // true
```

## Integration with SMS Gateway

### Twilio Example

```php
// In OtpService::sendViaSms()

use Twilio\Rest\Client;

public static function sendViaSms(string $phone, string $code): bool
{
    if (!config('services.twilio.enabled')) {
        // Fall back to mock
        return self::mockSms($phone, $code);
    }
    
    try {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        
        $twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => "Your OTP is: {$code}. Valid for 5 minutes."
        ]);
        
        return true;
    } catch (\Exception $e) {
        \Log::error('SMS send failed: ' . $e->getMessage());
        return false;
    }
}
```

### Configuration (config/services.php)

```php
'twilio' => [
    'enabled' => env('TWILIO_ENABLED', false),
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'from' => env('TWILIO_FROM'),
],
```

## Configuration Options

### Cache Driver
OTP uses Laravel's cache system. Configure in `.env`:

```env
CACHE_DRIVER=redis  # Recommended for production
# or
CACHE_DRIVER=file   # For local development
```

### Expiry Times
Customize in `config/app.php`:

```php
'otp' => [
    'login_expiry' => env('OTP_LOGIN_EXPIRY', 5),      // minutes
    'cod_expiry' => env('OTP_COD_EXPIRY', 15),         // minutes
    'default_expiry' => env('OTP_DEFAULT_EXPIRY', 10), // minutes
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 3),
],
```

### SMS Mock
Enable/disable in `.env`:

```env
SMS_MOCK_ENABLED=true
SMS_MOCK_LOG=true
```

## Existing Functionality Preserved

❌ **NO changes** to:
- Traditional password-based login
- User authentication system
- Registration flow
- Password reset flow
- Database structure
- Order processing

✅ **ONLY added**:
- Alternative login method (OTP)
- COD verification system
- OTP service layer
- New routes and controllers
- UI enhancements

## Best Practices

### For Developers
1. **Use cache wisely** - OTP relies on cache, ensure Redis/Memcached in production
2. **Log appropriately** - Log OTP sends but never log actual codes
3. **Test expiry** - Ensure cache driver respects TTL
4. **Rate limiting** - Add rate limiting middleware to OTP routes
5. **Monitor abuse** - Track failed OTP attempts

### For Users
1. **Never share OTP** - Educate users about OTP security
2. **Check expiry** - OTP expires quickly, use immediately
3. **Verify source** - Only enter OTP on official website
4. **Report issues** - Report suspicious OTP requests

### For Production
1. **Use Redis** - More reliable than file cache
2. **Enable queues** - Queue OTP emails for better performance
3. **Real SMS gateway** - Replace mock with Twilio/Nexmo
4. **Monitor logs** - Track OTP send failures
5. **Add analytics** - Track OTP usage patterns

## Troubleshooting

### OTP not received
- Check email configuration (`config/mail.php`)
- Verify queue is running (`php artisan queue:work`)
- Check logs (`storage/logs/laravel.log`)
- Ensure user email exists in database

### OTP expired too quickly
- Check cache driver configuration
- Verify system time is correct
- Increase expiry time if needed

### SMS not sending
- Check SMS mock log (`storage/logs/sms-mock.log`)
- Verify SMS gateway credentials (if using real gateway)
- Check phone number format

### OTP verification fails
- Ensure code is entered exactly (no spaces)
- Check if OTP expired
- Verify max attempts not exceeded
- Clear cache if testing (`php artisan cache:clear`)

## Next Steps

After STEP 12 completion, all core e-commerce features are implemented:
- ✅ Customer UI
- ✅ Broadcasting (real-time updates)
- ✅ File storage (public/private)
- ✅ Signed URLs (temporary access)
- ✅ OTP system (passwordless login + COD)

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- SMS mock log: `storage/logs/sms-mock.log`
- Test OTP in tinker: `php artisan tinker`
