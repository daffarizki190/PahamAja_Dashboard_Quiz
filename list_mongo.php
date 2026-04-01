<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- MONGODB QUIZ LIST ---\n";
try {
    $quizzes = DB::connection('mongodb')->table('quizzes')->get();
    echo "Found " . count($quizzes) . " quizzes.\n";
    foreach ($quizzes as $q) {
        $title = $q->title ?? ($q->name ?? 'Untitled');
        echo " - " . $title . " (Slug: " . ($q->slug ?? 'no-slug') . ")\n";
    }
} catch (\Exception $e) {
    echo "MongoDB Error: " . $e->getMessage() . "\n";
}
