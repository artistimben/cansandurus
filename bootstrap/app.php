<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\LogActivity;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware - tüm isteklerde çalışır
        $middleware->append(SecurityHeaders::class);
        
        // Web middleware grubuna ekle
        $middleware->web(append: [
            CheckActiveUser::class,
        ]);

        // Alias middleware - route'larda kullanılabilir
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'log.activity' => LogActivity::class,
        ]);

        // Rate limiting
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
