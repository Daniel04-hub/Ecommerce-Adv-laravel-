<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\DashboardApiController;
use App\Http\Controllers\Api\Admin\UsersApiController;
use App\Http\Controllers\Api\Admin\VendorsApiController;
use App\Http\Controllers\Api\Admin\ProductsApiController;
use App\Http\Controllers\Api\Admin\OrdersApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Admin API Routes - Protected by Sanctum and admin role
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    
    // Dashboard Statistics
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    
    // Users Management
    Route::get('/users', [UsersApiController::class, 'index']);
    Route::get('/users/{id}', [UsersApiController::class, 'show']);
    
    // Vendors Management
    Route::get('/vendors', [VendorsApiController::class, 'index']);
    Route::patch('/vendors/{id}/approve', [VendorsApiController::class, 'approve']);
    Route::patch('/vendors/{id}/block', [VendorsApiController::class, 'block']);
    
    // Products Management
    Route::get('/products', [ProductsApiController::class, 'index']);
    Route::get('/products/{id}', [ProductsApiController::class, 'show']);
    Route::post('/products/{id}/approve', [ProductsApiController::class, 'approve']);
    Route::post('/products/{id}/reject', [ProductsApiController::class, 'reject']);
    
    // Orders Monitoring
    Route::get('/orders', [OrdersApiController::class, 'index']);
    Route::get('/orders/{id}', [OrdersApiController::class, 'show']);
});
