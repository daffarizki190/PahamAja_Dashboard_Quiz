<?php

use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo 'Quizzes: '.Quiz::count()."\n";
echo 'Participants: '.Participant::count()."\n";
echo 'Questions: '.Question::count()."\n";
echo 'Options: '.Option::count()."\n";

$quiz = Quiz::first();
if ($quiz) {
    echo 'First Quiz ID: '.$quiz->id.', Slug: '.$quiz->slug."\n";
    echo 'Questions for first quiz: '.$quiz->questions()->count()."\n";
} else {
    echo "NO QUIZZES FOUND\n";
}
