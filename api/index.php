<?php

// Fix storage and bootstrap/cache paths for Vercel
$storagePath = '/tmp/storage';
$cachePath = '/tmp/bootstrap/cache';

foreach ([$storagePath, $cachePath] as $p) {
    if (!is_dir($p)) {
        mkdir($p, 0755, true);
    }
}

foreach (['app', 'framework/sessions', 'framework/views', 'framework/cache', 'logs'] as $sub) {
    $dir = $storagePath . '/' . $sub;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Ensure the environment variables for storage are set
putenv('APP_STORAGE=' . $storagePath);
putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('SESSION_PATH=' . $storagePath . '/framework/sessions');
putenv('LOG_CHANNEL=stderr');

// Redirect Laravel 11 bootstrap cache
putenv('APP_SERVICES_CACHE=' . $cachePath . '/services.php');
putenv('APP_PACKAGES_CACHE=' . $cachePath . '/packages.php');
putenv('APP_CONFIG_CACHE=' . $cachePath . '/config.php');
putenv('APP_ROUTES_CACHE=' . $cachePath . '/routes.php');
putenv('APP_EVENTS_CACHE=' . $cachePath . '/events.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new \Exception("Vendor directory missing at $autoloadPath");
    }
    require $autoloadPath;
    
    // Explicitly set the application instance
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Force storage path to /tmp for Vercel
    $app->useStoragePath('/tmp/storage');
    
    // Ensure critical env vars are set BEFORE handling
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('CACHE_DRIVER=array');
    putenv('FILESYSTEM_DISK=public');
    
    // Handle the request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Fatal Error Caught by Vercel Entry Point</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<h2>Stack Trace:</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
