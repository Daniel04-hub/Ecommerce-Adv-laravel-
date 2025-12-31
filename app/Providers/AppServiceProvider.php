<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Models\Order;
use App\Observers\OrderObserver;

use App\Events\OrderPlaced;
use App\Listeners\StartPaymentFlow;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    if (method_exists(Route::getFacadeRoot(), 'aliasMiddleware')) {
        Route::aliasMiddleware('role', RoleMiddleware::class);
        Route::aliasMiddleware('permission', PermissionMiddleware::class);
        Route::aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }

    // Register model observers
    Order::observe(OrderObserver::class);

    // Rate limiting definitions
    RateLimiter::for('api', function ($request) {
        $key = ($request->user()?->id ?? $request->ip());
        return [
            Limit::perMinute(60)->by($key),
        ];
    });

    // OTP requests (login) — stricter limits
    RateLimiter::for('otp', function ($request) {
        $identity = strtolower($request->input('email') ?? 'guest');
        $key = $request->ip() . '|' . $identity;
        return [
            Limit::perMinute(3)->by($key),
        ];
    });

    // COD OTP generation / verification — stricter limits
    RateLimiter::for('otp-cod', function ($request) {
        $orderId = (string) ($request->route('order')?->id ?? $request->input('order_id') ?? 'unknown');
        $key = $request->ip() . '|order:' . $orderId;
        return [
            Limit::perMinute(2)->by($key),
        ];
    });
}
}