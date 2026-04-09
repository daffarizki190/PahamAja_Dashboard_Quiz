<?php

// Use a simplified check without full Laravel bootstrap if it fails
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $apiKey = env('GEMINI_API_KEY');
    echo "API Key check: " . (empty($apiKey) ? "EMPTY!" : "PRESENT") . "\n";

    $response = Illuminate\Support\Facades\Http::timeout(30)
        ->withQueryParameters(['key' => $apiKey])
        ->get('https://generativelanguage.googleapis.com/v1beta/models');

    if ($response->successful()) {
        $models = $response->json('models');
        echo "Available Models:\n";
        foreach ($models as $m) {
            echo "- " . $m['name'] . "\n";
        }
    } else {
        echo "Error fetching models: " . $response->status() . "\n";
        echo $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
