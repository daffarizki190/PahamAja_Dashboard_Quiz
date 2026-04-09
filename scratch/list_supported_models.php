<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AiGeneratorService;

$service = new AiGeneratorService();
$models = $service->listAvailableModels();

foreach ($models as $m) {
    if (in_array('generateContent', $m['supportedGenerationMethods'])) {
        echo $m['name'] . "\n";
    }
}
