<?php

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->registered(function ($app) {
        if (env('VERCEL')) {
            if (!class_exists('Route')) {
                class_alias(\Illuminate\Support\Facades\Route::class, 'Route');
            }
            if (!class_exists('URL')) {
                class_alias(\Illuminate\Support\Facades\URL::class, 'URL');
            }
            if (!class_exists('Config')) {
                class_alias(\Illuminate\Support\Facades\Config::class, 'Config');
            }
        }
    })
    ->create();
