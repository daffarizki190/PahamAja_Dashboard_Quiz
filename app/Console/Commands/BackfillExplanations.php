<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Services\AiGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillExplanations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:backfill-explanations {--force : Force backfill all questions even if they have explanations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate real AI explanations for questions with placeholder text';

    /**
     * Execute the console command.
     */
    public function handle(AiGeneratorService $aiService)
    {
        $placeholder = 'Ini adalah penjelasan logis dari AI';
        
        $query = Question::with(['options', 'quiz']);

        if (!$this->option('force')) {
            $query->where(function ($q) use ($placeholder) {
                $q->whereNull('explanation')
                  ->orWhere('explanation', 'like', $placeholder . '%')
                  ->orWhere('explanation', '');
            });
        }

        $questions = $query->get();

        if ($questions->isEmpty()) {
            $this->info('Tidak ada soal yang butuh diisi penjelasannya.');
            return;
        }

        $this->info("Ditemukan {$questions->count()} soal. Memulai pengisian penjelasan AI...");
        $bar = $this->output->createProgressBar($questions->count());
        $bar->start();

        foreach ($questions as $question) {
            try {
                $correctOption = $question->options->where('is_correct', true)->first();
                $allOptions = $question->options->pluck('text')->toArray();
                
                if (!$correctOption) {
                    Log::warning("Soal ID {$question->id} tidak punya jawaban benar. Dilewati.");
                    $bar->advance();
                    continue;
                }

                $prompt = "Tugas: Berikan penjelasan logis (dalam Bahasa Indonesia) untuk soal kuis berikut.\n";
                $prompt .= "Kuis: {$question->quiz->title}\n";
                $prompt .= "Pertanyaan: {$question->text}\n";
                $prompt .= "Pilihan Jawaban:\n- " . implode("\n- ", $allOptions) . "\n";
                $prompt .= "Jawaban Benar: " . $correctOption->text . "\n\n";
                $prompt .= "Berikan penjelasan yang ringkas dan profesional tentang mengapa jawaban tersebut benar dan logika di baliknya. Berikan HANYA teks penjelasan tanpa awalan apa pun.";

                $explanation = $aiService->generateInsight($prompt, 700);
                
                // Clean up any "Penjelasan:" or quotes prefix if AI adds them
                $explanation = preg_replace('/^Penjelasan:\s*/i', '', trim($explanation, " \t\n\r\0\x0B\"'"));

                $question->update(['explanation' => $explanation]);
                
                // Short sleep to avoid heavy rate limits if processing many
                usleep(500000); 

            } catch (\Exception $e) {
                $this->error("\nError pada soal ID {$question->id}: " . $e->getMessage());
                Log::error("Backfill failed for question {$question->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Penjelasan AI telah diperbarui.');
    }
}
