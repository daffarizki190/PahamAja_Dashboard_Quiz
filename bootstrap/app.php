<?php

use App\Http\Middleware\AdminAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin.auth' => AdminAuth::class,
            'nocache' => \App\Http\Middleware\ForceNoCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->registered(function ($app) {
        if (env('VERCEL')) {
            if (! class_exists('Route')) {
                class_alias(Route::class, 'Route');
            }
            if (! class_exists('URL')) {
                class_alias(URL::class, 'URL');
            }
            if (! class_exists('Config')) {
                class_alias(Config::class, 'Config');
            }
            URL::forceScheme('https');
        }
    })
    ->create();
