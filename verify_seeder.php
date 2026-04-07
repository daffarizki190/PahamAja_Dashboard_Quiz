<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Database\Seeders\DatabaseSeeder;

try {
    echo "Starting seeder...\n";
    (new DatabaseSeeder())->run();
    echo "Seeding completed successfully!\n";
} catch (\Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
