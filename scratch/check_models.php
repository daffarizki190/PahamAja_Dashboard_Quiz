<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AiGeneratorService;

$service = new AiGeneratorService();
$models = $service->listAvailableModels();

echo "Available Models:\n";
foreach ($models as $m) {
    echo "- " . $m['name'] . " (" . $m['displayName'] . ")\n";
}
