<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || defined('PHPUNIT_VERSION')) {
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => ':memory:',
                'session.driver' => 'array',
                'cache.default' => 'array',
                'queue.default' => 'sync',
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
