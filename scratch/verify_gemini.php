<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AiGeneratorService;

try {
    $service = new AiGeneratorService();
    echo "Using model: " . $service->listAvailableModels()[0]['name'] . " (example)\n";
    
    $result = $service->generateInsight("Say 'Gemini Fix Successful' if you can read this.");
    echo "Response: " . $result . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
