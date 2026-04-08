<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "GEMINI_MODEL Config: " . config('services.gemini.model') . "\n";
echo "GEMINI_MODEL Env: " . env('GEMINI_MODEL') . "\n";
