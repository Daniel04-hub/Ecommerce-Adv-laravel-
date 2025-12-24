<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\ProductBrowseController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\OrderSuccessController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Admin\ProductApprovalController;
use App\Http\Controllers\Auth\GoogleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

// Google OAuth (guest)
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('oauth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('oauth.google.callback');

/*
|--------------------------------------------------------------------------
| Customer Product Browsing (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductBrowseController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product}', [ProductBrowseController::class, 'show'])
    ->name('products.show');

/*
|--------------------------------------------------------------------------
| Shopping Cart (NO AUTH - Session Based)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout (NO AUTH - Session Based)
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

/*
|--------------------------------------------------------------------------
| Mock Payment (NO AUTH - Session Based)
|--------------------------------------------------------------------------
*/
Route::get('/payment/mock', [PaymentController::class, 'mockShow'])->name('payment.mock.show');
Route::post('/payment/mock/process', [PaymentController::class, 'mockProcess'])->name('payment.mock.process');
Route::get('/payment/mock/process', function () {
    return redirect()->route('payment.mock.show');
});

/*
|--------------------------------------------------------------------------
| Order Success (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/order/success', [OrderSuccessController::class, 'show'])->name('order.success');

/*
|--------------------------------------------------------------------------
| Signed URLs (Temporary Access Links)
|--------------------------------------------------------------------------
*/
Route::middleware('signed')->group(function () {
    // Temporary invoice download (no auth required, signature is the auth)
    Route::get('/signed/invoice/{order}/download', [\App\Http\Controllers\SignedDownloadController::class, 'downloadInvoice'])
        ->name('signed.invoice.download');
    
    Route::get('/signed/invoice/{order}', [\App\Http\Controllers\SignedDownloadController::class, 'viewInvoice'])
        ->name('signed.invoice.view');

    // Temporary shipping label download
    Route::get('/signed/shipping-label/{order}/download', [\App\Http\Controllers\SignedDownloadController::class, 'downloadShippingLabel'])
        ->name('signed.shipping-label.download');
    
    Route::get('/signed/shipping-label/{order}', [\App\Http\Controllers\SignedDownloadController::class, 'viewShippingLabel'])
        ->name('signed.shipping-label.view');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard: role-aware redirect to keep flow consistent
    Route::get('/dashboard', function () {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user?->hasRole('vendor')) {
            return redirect()->route('vendor.dashboard');
        }
        if ($user?->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Customer Orders
    |--------------------------------------------------------------------------
    */
    Route::post('/products/{product}/order', [OrderController::class, 'store'])
        ->middleware('role:customer')
        ->name('orders.store');

    // Beginner-friendly alias: POST /orders with product_id
    Route::post('/orders', [OrderController::class, 'storeDirect'])
        ->middleware('role:customer')
        ->name('orders.storeDirect');

    // Order success page
    Route::get('/orders/success', function () {
        return view('orders.success');
    })->middleware('auth')->name('orders.success');

    // Customer Orders
    Route::get('/customer/orders', [OrderController::class, 'index'])
        ->middleware('role:customer')
        ->name('customer.orders.index');

    // View individual order with real-time status
    Route::get('/customer/orders/{order}', [OrderController::class, 'show'])
        ->middleware('role:customer')
        ->name('customer.orders.show');

    // Customer Invoice Downloads (Private Files)
    Route::get('/customer/orders/{order}/invoice/download', [\App\Http\Controllers\Customer\InvoiceController::class, 'download'])
        ->middleware('role:customer')
        ->name('customer.orders.invoice.download');

    Route::get('/customer/orders/{order}/invoice', [\App\Http\Controllers\Customer\InvoiceController::class, 'view'])
        ->middleware('role:customer')
        ->name('customer.orders.invoice.view');

    // Generate Temporary Signed URLs (API endpoints)
    Route::post('/customer/orders/{order}/generate-signed-invoice-url', [\App\Http\Controllers\Api\SignedUrlGeneratorController::class, 'generateInvoiceUrl'])
        ->middleware('role:customer')
        ->name('customer.orders.generate-signed-invoice-url');

    /*
    |--------------------------------------------------------------------------
    | Vendor Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('vendor')->name('vendor.')->middleware('role:vendor')->group(function () {
        // Vendor Dashboard (Controller-based for MVC)
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])
            ->name('dashboard');

        // Vendor Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/{product}/stock', [ProductController::class, 'editStock'])->name('products.stock');
        Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');

        // Vendor Orders
        Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/{status}', [VendorOrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');

        // Vendor Shipping Labels (Private Files)
        Route::get('/orders/{order}/shipping-label/download', [\App\Http\Controllers\Vendor\ShippingLabelController::class, 'download'])
            ->name('orders.shipping-label.download');
        Route::get('/orders/{order}/shipping-label', [\App\Http\Controllers\Vendor\ShippingLabelController::class, 'view'])
            ->name('orders.shipping-label.view');
        Route::post('/orders/{order}/shipping-label/generate', [\App\Http\Controllers\Vendor\ShippingLabelController::class, 'generate'])
            ->name('orders.shipping-label.generate');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // Admin Users Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
            ->name('users.index');

        Route::get('/products/pending', [ProductApprovalController::class, 'index'])
            ->name('products.pending');

        Route::post('/products/{product}/approve', [ProductApprovalController::class, 'approve'])
            ->name('products.approve');

        Route::post('/products/{product}/reject', [ProductApprovalController::class, 'reject'])
            ->name('products.reject');

        // Admin Vendor Management
        Route::get('/vendors', [\App\Http\Controllers\Admin\VendorManagementController::class, 'index'])
            ->name('vendors.index');
        Route::patch('/vendors/{vendor}/approve', [\App\Http\Controllers\Admin\VendorManagementController::class, 'approve'])
            ->name('vendors.approve');
        Route::patch('/vendors/{vendor}/block', [\App\Http\Controllers\Admin\VendorManagementController::class, 'block'])
            ->name('vendors.block');

        // Admin Order Monitoring (read-only)
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderMonitoringController::class, 'index'])
            ->name('orders.index');
    });
});

/*
|--------------------------------------------------------------------------
| OTP Login Routes (Alternative Login Method)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login/otp', [\App\Http\Controllers\Auth\OtpLoginController::class, 'showRequestForm'])
        ->name('otp.request');
    Route::post('/login/otp/send', [\App\Http\Controllers\Auth\OtpLoginController::class, 'sendOtp'])
        ->name('otp.send');
    Route::get('/login/otp/verify', [\App\Http\Controllers\Auth\OtpLoginController::class, 'showVerifyForm'])
        ->name('otp.verify.form');
    Route::post('/login/otp/verify', [\App\Http\Controllers\Auth\OtpLoginController::class, 'verifyOtp'])
        ->name('otp.verify');
    Route::post('/login/otp/resend', [\App\Http\Controllers\Auth\OtpLoginController::class, 'resendOtp'])
        ->name('otp.resend');
});

/*
|--------------------------------------------------------------------------
| COD Verification Routes
|--------------------------------------------------------------------------
*/
// Customer: Generate COD OTP for their order
Route::middleware(['auth'])->group(function () {
    Route::post('/orders/{order}/cod/generate-otp', [\App\Http\Controllers\CodVerificationController::class, 'generateOtp'])
        ->name('cod.generate');
    Route::get('/orders/{order}/cod/status', [\App\Http\Controllers\CodVerificationController::class, 'checkOtpStatus'])
        ->name('cod.status');
});

// Delivery Person: Verify COD OTP (no auth required)
Route::get('/cod/verify', [\App\Http\Controllers\CodVerificationController::class, 'showVerificationForm'])
    ->name('cod.verify.form');
Route::post('/cod/verify', [\App\Http\Controllers\CodVerificationController::class, 'verifyOtp'])
    ->name('cod.verify');

require __DIR__.'/auth.php';
