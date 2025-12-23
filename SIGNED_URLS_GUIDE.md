# STEP 11 — ONE-TIME SIGNED URLS — IMPLEMENTATION GUIDE

## Overview
Secure temporary access links with signature validation and expiry for invoices, shipping labels, and password resets.

## Key Concepts

### What are Signed URLs?
Signed URLs contain a cryptographic signature that:
- ✅ Prevents URL tampering
- ✅ Expires after a set time
- ✅ Works without authentication (signature IS the auth)
- ✅ Cannot be reused or modified

### Use Cases
1. **Temporary Invoice Download** - Share invoice with accountant/client
2. **Password Reset Links** - Secure, expiring reset links
3. **Shipping Label Access** - One-time vendor access
4. **Guest Access** - Temporary file access without login

## Files Created

### 1. **app/Services/SignedUrlService.php** (NEW)
Universal signed URL generation service.

**Methods:**
```php
// Generate temporary invoice URL (expires in X minutes)
SignedUrlService::generateInvoiceUrl($orderId, $expiresInMinutes);

// Generate temporary shipping label URL
SignedUrlService::generateShippingLabelUrl($orderId, $expiresInMinutes);

// Generate password reset URL (Laravel native)
SignedUrlService::generatePasswordResetUrl($email, $token);

// Generate any temporary link
SignedUrlService::generateTemporaryLink($routeName, $params, $expiresInMinutes);

// Generate permanent signed URL (no expiry)
SignedUrlService::generatePermanentSignedUrl($routeName, $params);

// One-time tokens (stored in cache, deleted after use)
SignedUrlService::generateOneTimeToken($identifier, $expiresInMinutes);
SignedUrlService::verifyOneTimeToken($token);

// Validation helpers
SignedUrlService::isValidSignedUrl($request);
SignedUrlService::hasExpired($request);
```

### 2. **app/Http/Controllers/SignedDownloadController.php** (NEW)
Handles signed URL downloads without authentication.

**Routes Protected:**
- `GET /signed/invoice/{order}/download` - Download invoice via signed URL
- `GET /signed/invoice/{order}` - View invoice via signed URL
- `GET /signed/shipping-label/{order}/download` - Download shipping label
- `GET /signed/shipping-label/{order}` - View shipping label

**Security:**
- No auth required (signature validates access)
- Middleware validates signature
- Expires after set time
- Cannot be tampered with

### 3. **app/Http/Middleware/ValidateSignedUrl.php** (NEW)
Custom middleware for friendly error handling.

**Features:**
- Checks signature validity
- Detects if expired vs invalid
- Shows custom error pages
- Provides helpful recovery options

### 4. **app/Mail/InvoiceReadyMail.php** (NEW)
Mailable for sending invoice with temporary signed URL.

**Usage:**
```php
use App\Mail\InvoiceReadyMail;
use Illuminate\Support\Facades\Mail;

Mail::to($order->email)->send(new InvoiceReadyMail($order, 60)); // 60 min expiry
```

**Email Contains:**
- Order details
- Temporary download link
- Expiry warning
- Link to login for permanent access

### 5. **resources/views/emails/invoice-ready.blade.php** (NEW)
Professional HTML email template with:
- Gradient header
- Order summary
- Download button
- Expiry warning
- Alternative login link

### 6. **app/Notifications/CustomResetPasswordNotification.php** (NEW)
Extended Laravel's password reset with enhanced messaging.

**Features:**
- Uses Laravel's built-in signed routes
- Custom email message
- One-time use emphasis
- Security warnings

### 7. **resources/views/errors/link-expired.blade.php** (NEW)
Friendly error page for expired links.

**Shows:**
- Clear expiry message
- Helpful recovery actions
- Login button (if not authenticated)
- Back to shop link

### 8. **resources/views/errors/link-invalid.blade.php** (NEW)
Error page for invalid/tampered links.

**Shows:**
- Security warning
- Invalid signature message
- Contact support link
- Recovery options

### 9. **app/Http/Controllers/SignedUrlGeneratorController.php** (NEW)
Helper controller for generating signed URLs dynamically.

**API Endpoints:**
```php
POST /api/generate-invoice-url
POST /api/generate-shipping-label-url
```

**Usage in Frontend:**
```javascript
fetch('/api/generate-invoice-url', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        order_id: 123,
        expires_in: 60 // minutes
    })
}).then(res => res.json());
```

## Files Modified

### 1. **routes/web.php** (MODIFIED)
Added signed URL routes:

```php
Route::middleware('signed')->group(function () {
    Route::get('/signed/invoice/{order}/download', [SignedDownloadController::class, 'downloadInvoice'])
        ->name('signed.invoice.download');
    
    Route::get('/signed/invoice/{order}', [SignedDownloadController::class, 'viewInvoice'])
        ->name('signed.invoice.view');

    Route::get('/signed/shipping-label/{order}/download', [SignedDownloadController::class, 'downloadShippingLabel'])
        ->name('signed.shipping-label.download');
    
    Route::get('/signed/shipping-label/{order}', [SignedDownloadController::class, 'viewShippingLabel'])
        ->name('signed.shipping-label.view');
});
```

### 2. **resources/views/customer/orders/show.blade.php** (MODIFIED)
Added "Generate Share Link" button with modal:
- Select expiry time (1h, 6h, 12h, 24h)
- Generate temporary signed URL
- Copy to clipboard
- Shows expiry time

## Usage Examples

### 1. Send Invoice Email with Temporary Link

```php
use App\Mail\InvoiceReadyMail;
use Illuminate\Support\Facades\Mail;

$order = Order::find(1);

// Send email with 60-minute link
Mail::to($order->email)->send(new InvoiceReadyMail($order, 60));
```

### 2. Generate Temporary Invoice URL Manually

```php
use App\Services\SignedUrlService;

$order = Order::find(1);

// Generate 2-hour temporary link
$url = SignedUrlService::generateInvoiceUrl($order->id, 120);

// Share with customer/accountant
echo $url;
// Output: https://yoursite.com/signed/invoice/1/download?expires=...&signature=...
```

### 3. Verify Signed URL in Controller

```php
use App\Services\SignedUrlService;

public function download(Request $request)
{
    if (!SignedUrlService::isValidSignedUrl($request)) {
        abort(403, 'Invalid or expired link');
    }
    
    // Proceed with download
}
```

### 4. Create One-Time Token

```php
use App\Services\SignedUrlService;

// Generate one-time token for sensitive operation
$token = SignedUrlService::generateOneTimeToken('user-123-action', 15); // 15 min

// Send token to user
$url = url('/verify-action?token=' . $token);

// Later, verify and consume token
$data = SignedUrlService::verifyOneTimeToken($token);
if ($data) {
    // Token valid, proceed
    // Token is now deleted (one-time use)
} else {
    // Token invalid or already used
}
```

### 5. Generate Temporary Link for Any Route

```php
use App\Services\SignedUrlService;

// Generate temporary link for custom route
$url = SignedUrlService::generateTemporaryLink(
    'download.report',
    ['report' => 123],
    30 // 30 minutes
);
```

## Security Features

### Signature Validation
✅ **Cryptographic Hash** - Uses Laravel's APP_KEY for signing
✅ **Tamper-Proof** - Changing any part invalidates signature
✅ **Time-Limited** - Automatic expiry after set time
✅ **No Database** - Validation happens without DB queries

### Middleware Protection
```php
Route::middleware('signed')->group(function () {
    // All routes here require valid signature
});
```

### Error Handling
- **Expired Links** → Friendly "Link Expired" page
- **Invalid Links** → Security warning page
- **Tampered URLs** → Automatic rejection

### Best Practices
✅ Use short expiry times (1-24 hours)
✅ One-time tokens for sensitive operations
✅ Log signed URL access for auditing
✅ Provide alternative authenticated access
✅ Clear user communication about expiry

## Testing

### Test Signed URL Generation

```php
use App\Services\SignedUrlService;

$url = SignedUrlService::generateInvoiceUrl(1, 5); // 5 min expiry
echo $url;

// Visit URL immediately → Should work
// Wait 6 minutes → Should show "Link Expired"
```

### Test Invalid Signature

```php
$url = SignedUrlService::generateInvoiceUrl(1, 60);

// Modify URL parameters manually
$tamperedUrl = str_replace('order=1', 'order=2', $url);

// Visit tampered URL → Should show "Invalid Link"
```

### Test Email Sending

```bash
php artisan tinker

>>> $order = Order::first();
>>> Mail::to($order->email)->send(new InvoiceReadyMail($order, 60));
// Check email inbox for temporary link
```

### Test One-Time Token

```php
use App\Services\SignedUrlService;

$token = SignedUrlService::generateOneTimeToken('test-123', 15);

// First use
$data = SignedUrlService::verifyOneTimeToken($token);
// Returns: ['identifier' => 'test-123', 'created_at' => ...]

// Second use (same token)
$data = SignedUrlService::verifyOneTimeToken($token);
// Returns: null (token already consumed)
```

## Laravel Native Features Used

### Password Reset (Already Signed)
Laravel's built-in password reset already uses signed URLs:

```php
// In User model
public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPasswordNotification($token));
}
```

### Email Verification (Already Signed)
Laravel's email verification also uses signed URLs:

```php
// Built-in route
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed']) // Requires valid signature
    ->name('verification.verify');
```

## Configuration

### Expiry Times (Recommended)

```php
'invoice_link_expiry' => 60,        // 1 hour
'shipping_link_expiry' => 360,      // 6 hours
'password_reset_expiry' => 60,      // 1 hour (Laravel default)
'one_time_token_expiry' => 15,      // 15 minutes
```

Add to `config/app.php`:

```php
'signed_urls' => [
    'invoice_expiry' => env('INVOICE_LINK_EXPIRY', 60),
    'shipping_expiry' => env('SHIPPING_LINK_EXPIRY', 360),
    'token_expiry' => env('ONE_TIME_TOKEN_EXPIRY', 15),
],
```

## Frontend Integration

### Share Link Modal (Included in Order View)
```blade
<!-- Button to open modal -->
<button data-bs-toggle="modal" data-bs-target="#shareInvoiceModal">
    Generate Share Link
</button>

<!-- Modal generates temporary link -->
<!-- User can select expiry time -->
<!-- Copy to clipboard functionality -->
```

### JavaScript Copy Function
```javascript
function copyToClipboard() {
    const input = document.getElementById('generatedLink');
    input.select();
    document.execCommand('copy');
    // Show feedback
}
```

## Existing Functionality Preserved

❌ **NO changes** to:
- Authentication system
- Password reset flow (only enhanced messaging)
- Order processing
- File storage logic
- Database structure

✅ **ONLY added**:
- Signed URL generation service
- Temporary download routes
- Error handling pages
- Email notifications
- UI share buttons

## Next Steps

- Confirm STEP 11 is complete
- Ready for STEP 12 (OTP System)
