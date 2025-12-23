# STEP 10 — FILE STORAGE SYSTEM — IMPLEMENTATION GUIDE

## Overview
Secure file storage separation for public and private files with enterprise-grade access control.

## Storage Architecture

### Public Storage (`storage/app/public`)
- **Product images** (already implemented, reused)
- **Public assets**
- Accessible via `/storage` URL after `php artisan storage:link`

### Private Storage (`storage/app/private`)
- **Invoices** → `storage/app/private/invoices`
- **Shipping Labels** → `storage/app/private/shipping`
- **Warehouse Documents** → `storage/app/private/warehouse`
- Requires authentication and authorization to access

## Files Created/Modified

### 1. **config/filesystems.php** (MODIFIED)
Added specialized storage disks:

```php
'private' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'visibility' => 'private',
],

'invoices' => [
    'driver' => 'local',
    'root' => storage_path('app/private/invoices'),
    'visibility' => 'private',
],

'shipping' => [
    'driver' => 'local',
    'root' => storage_path('app/private/shipping'),
    'visibility' => 'private',
],

'warehouse' => [
    'driver' => 'local',
    'root' => storage_path('app/private/warehouse'),
    'visibility' => 'private',
],
```

### 2. **app/Services/FileStorageService.php** (NEW)
Universal file storage helper with methods:

**Public Storage:**
```php
FileStorageService::storePublic($file, 'images'); // Store in public
FileStorageService::getPublicUrl($path);          // Get public URL
```

**Private Storage:**
```php
FileStorageService::storePrivate($file, 'invoices', 'directory');
FileStorageService::storeInvoice($file, $orderId);
FileStorageService::storeShippingLabel($file, $orderId);
FileStorageService::storeWarehouseDocument($file, 'type');
```

**File Operations:**
```php
FileStorageService::exists($path, $disk);
FileStorageService::get($path, $disk);
FileStorageService::delete($path, $disk);
FileStorageService::download($path, $disk, $filename);
FileStorageService::size($path, $disk);
FileStorageService::lastModified($path, $disk);
FileStorageService::listFiles($directory, $disk);
```

### 3. **app/Services/InvoiceService.php** (NEW)
Invoice generation and management:

```php
// Generate invoice for order
InvoiceService::generate($order);

// Check if invoice exists
InvoiceService::exists($order);

// Download invoice
InvoiceService::download($order);

// Generate if not exists
InvoiceService::generateIfNotExists($order);

// Get invoice path
InvoiceService::getInvoicePath($order);
```

**Features:**
- Auto-generates HTML invoices
- Stores in private `invoices` disk
- Includes order details, customer info, pricing
- Ready for PDF conversion (future enhancement)

### 4. **app/Services/ShippingLabelService.php** (NEW)
Shipping label generation:

```php
// Generate shipping label
ShippingLabelService::generate($order);

// Check if exists
ShippingLabelService::exists($order);

// Download label
ShippingLabelService::download($order);

// Generate if not exists
ShippingLabelService::generateIfNotExists($order);

// Generate tracking number
ShippingLabelService::generateTrackingNumber($order);
```

**Features:**
- Auto-generates HTML shipping labels
- Includes tracking number (TRK + order ID)
- Barcode placeholder for integration
- Ship-to and ship-from addresses

### 5. **app/Http/Controllers/Customer/InvoiceController.php** (NEW)
Customer invoice access with authorization:

**Routes:**
- `GET /customer/orders/{order}/invoice/download` - Download invoice
- `GET /customer/orders/{order}/invoice` - View invoice in browser

**Security:**
✅ Verifies customer owns the order
✅ Only authenticated customers can access
✅ Generates invoice on-the-fly if missing

### 6. **app/Http/Controllers/Vendor/ShippingLabelController.php** (NEW)
Vendor shipping label access:

**Routes:**
- `GET /vendor/orders/{order}/shipping-label/download` - Download label
- `GET /vendor/orders/{order}/shipping-label` - View label
- `POST /vendor/orders/{order}/shipping-label/generate` - Generate label

**Security:**
✅ Only vendors can access
✅ Validates vendor role
✅ Generates label on-demand

### 7. **routes/web.php** (MODIFIED)
Added secure download routes:

**Customer Routes:**
```php
Route::get('/customer/orders/{order}/invoice/download', [InvoiceController::class, 'download'])
    ->middleware('role:customer')
    ->name('customer.orders.invoice.download');

Route::get('/customer/orders/{order}/invoice', [InvoiceController::class, 'view'])
    ->middleware('role:customer')
    ->name('customer.orders.invoice.view');
```

**Vendor Routes:**
```php
Route::get('/vendor/orders/{order}/shipping-label/download', [ShippingLabelController::class, 'download'])
    ->name('vendor.orders.shipping-label.download');

Route::get('/vendor/orders/{order}/shipping-label', [ShippingLabelController::class, 'view'])
    ->name('vendor.orders.shipping-label.view');

Route::post('/vendor/orders/{order}/shipping-label/generate', [ShippingLabelController::class, 'generate'])
    ->name('vendor.orders.shipping-label.generate');
```

### 8. **resources/views/customer/orders/show.blade.php** (MODIFIED)
Added "Documents" card with invoice download buttons.

## Usage Examples

### Store Public File (Product Image)
```php
use App\Services\FileStorageService;

$path = FileStorageService::storePublic($request->file('image'), 'products');
// Stored in: storage/app/public/products/filename.jpg

$url = FileStorageService::getPublicUrl($path);
// Returns: http://yoursite.com/storage/products/filename.jpg
```

### Store Private File (Invoice)
```php
use App\Services\InvoiceService;

// Auto-generates and stores invoice
$path = InvoiceService::generate($order);
// Stored in: storage/app/private/invoices/invoice-123-20251218-143022.html
```

### Store Warehouse Document
```php
use App\Services\FileStorageService;

$path = FileStorageService::storeWarehouseDocument(
    $request->file('document'), 
    'packing-slip'
);
// Stored in: storage/app/private/warehouse/packing-slip-20251218-143022.pdf
```

### Download Private File (Authorized)
```php
// In controller:
return FileStorageService::download($path, 'invoices', 'invoice.html');
```

## Security Features

### Access Control
✅ **Customer Invoices** — Only order owner can download
✅ **Vendor Shipping Labels** — Only vendors can access
✅ **Warehouse Documents** — Admin/vendor only (implement as needed)
✅ **Private Disk** — No direct URL access, requires authentication

### Authorization Flow
```
1. User requests private file
   ↓
2. Route middleware checks authentication
   ↓
3. Controller verifies ownership/role
   ↓
4. Service retrieves file from private storage
   ↓
5. File streamed to authorized user only
```

### File Isolation
- Public files: `/storage/app/public` (symlinked to `/public/storage`)
- Private files: `/storage/app/private` (no public access)
- Dedicated subdirectories for each document type

## Directory Structure

```
storage/
├── app/
│   ├── public/              # Public storage (already exists)
│   │   ├── products/        # Product images (existing)
│   │   └── ...
│   └── private/             # Private storage (NEW)
│       ├── invoices/        # Customer invoices
│       ├── shipping/        # Shipping labels
│       └── warehouse/       # Warehouse documents
```

## Testing

### Test Invoice Generation
```php
use App\Services\InvoiceService;
use App\Models\Order;

$order = Order::find(1);
$path = InvoiceService::generate($order);
// Check: storage/app/private/invoices/invoice-1-*.html
```

### Test Shipping Label
```php
use App\Services\ShippingLabelService;

$label = ShippingLabelService::generate($order);
// Check: storage/app/private/shipping/shipping-label-1-*.html
```

### Test Customer Download
1. Login as customer
2. Navigate to: `/customer/orders/{order_id}`
3. Click "Download Invoice"
4. Verify download works
5. Try accessing another customer's invoice → Should fail (403)

### Test File Storage
```php
// In tinker or test:
use Illuminate\Support\Facades\Storage;

Storage::disk('invoices')->exists('invoice-1.html'); // true/false
Storage::disk('shipping')->files(); // List all shipping labels
```

## Future Enhancements

### PDF Conversion
Currently generates HTML. To convert to PDF:

```bash
composer require barryvdh/laravel-dompdf
```

Then modify services:
```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadHTML($html);
$pdf->save(storage_path('app/private/invoices/invoice-' . $order->id . '.pdf'));
```

### Cloud Storage (S3)
Update `.env`:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

No code changes needed! Services use `Storage` facade.

## Existing Functionality Preserved

❌ **NO changes** to:
- Product image upload logic
- Existing file storage for products
- Database structure
- Authentication system
- Order processing flow

✅ **ONLY added**:
- New storage disks for private files
- Helper services for file management
- Secure download routes with authorization
- Invoice/shipping label generation

## Next Steps

- Confirm STEP 10 is complete
- Ready for STEP 11 (One-Time Signed URLs)
