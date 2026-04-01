<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Quizzes: " . \App\Models\Quiz::count() . "\n";
echo "Participants: " . \App\Models\Participant::count() . "\n";
echo "Questions: " . \App\Models\Question::count() . "\n";
echo "Options: " . \App\Models\Option::count() . "\n";

$quiz = \App\Models\Quiz::first();
if ($quiz) {
    echo "First Quiz ID: " . $quiz->id . ", Slug: " . $quiz->slug . "\n";
    echo "Questions for first quiz: " . $quiz->questions()->count() . "\n";
} else {
    echo "NO QUIZZES FOUND\n";
}
