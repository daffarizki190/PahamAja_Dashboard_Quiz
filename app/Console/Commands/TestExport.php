<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quiz;
use App\Exports\QuizExport;
use Maatwebsite\Excel\Facades\Excel;

class TestExport extends Command
{
    protected $signature = 'test:export';

    public function handle()
    {
        $this->info('Starting export test...');
        $quiz = Quiz::where('slug', 'penanganan-masalah-dan-insiden-FjAok')->first();
        Excel::store(new QuizExport($quiz), 'test_artisan.csv', 'local', \Maatwebsite\Excel\Excel::CSV);
        $this->info('Export finished!');
    }
}
