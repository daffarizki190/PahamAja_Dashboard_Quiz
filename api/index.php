<?php

// Fix storage path for Vercel
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/framework/views', 0755, true);
    mkdir($storagePath . '/framework/cache', 0755, true);
    mkdir($storagePath . '/logs', 0755, true);
}

// Ensure the environment variables for storage are set
putenv('APP_STORAGE=' . $storagePath);
putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('SESSION_DRIVER=file');
putenv('SESSION_PATH=' . $storagePath . '/framework/sessions');
putenv('LOG_CHANNEL=stderr');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    // Forward Vercel requests to normal Laravel entry point
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Fatal Error Caught by Vercel Entry Point</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<h2>Stack Trace:</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
