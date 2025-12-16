<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Spatie role/permission middlewares if not auto-registered
        if (method_exists(Route::getFacadeRoot(), 'aliasMiddleware')) {
            Route::aliasMiddleware('role', RoleMiddleware::class);
            Route::aliasMiddleware('permission', PermissionMiddleware::class);
            Route::aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
        }
    }
}
