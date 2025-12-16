<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\ProductBrowseController;
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

require __DIR__.'/auth.php';
