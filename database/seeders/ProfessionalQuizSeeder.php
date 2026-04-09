<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessionalQuizSeeder extends Seeder
{
    /**
     * Run the database seeds with TOTAL FAULT TOLERANCE.
     */
    public function run(): void
    {
        $quizzes = [
            [
                'title' => 'SOP Penanganan Masalah dan Insiden (Original)',
                'slug' => 'sop-penanganan-masalah-dan-insiden-profesional',
                'time_limit' => 20,
                'passing_score' => 80,
                'questions' => [
                    [
                        'text' => 'Apa tujuan utama dari SOP Penanganan Masalah dan Insiden?',
                        'options' => [
                            ['text' => 'Mengetahui kronologi kejadian dan penyelesaian masalah secara sistematis', 'is_correct' => true],
                            ['text' => 'Melaporkan insiden hanya kepada Leader', 'is_correct' => false],
                            ['text' => 'Mengidentifikasi Customer dan dokumennya saja', 'is_correct' => false],
                            ['text' => 'Membuat Berita Acara tanpa tindak lanjut', 'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Langkah pertama yang harus dilakukan saat terjadi insiden di lokasi kerja adalah?',
                        'options' => [
                            ['text' => 'Mengamankan area dan memberikan pertolongan pertama jika ada korban', 'is_correct' => true],
                            ['text' => 'Langsung menelepon manajemen pusat', 'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Siapa yang berhak menandatangani Berita Acara penyelesaian insiden di lapangan?',
                        'options' => [
                            ['text' => 'Petugas terkait, Leader, dan saksi jika ada', 'is_correct' => true],
                            ['text' => 'Pihak luar yang tidak sengaja lewat', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Penanganan Keluhan Pelanggan (Professional)',
                'slug' => 'penanganan-keluhan-pelanggan-profesional',
                'time_limit' => 15,
                'passing_score' => 75,
                'questions' => [
                    [
                        'text' => 'Apa langkah pertama yang harus dilakukan saat menghadapi pelanggan yang sedang marah?',
                        'options' => [
                            ['text' => 'Mendengarkan dengan penuh empati tanpa memotong pembicaraan', 'is_correct' => true],
                            ['text' => 'Langsung memberikan bantahan jika pelanggan salah', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Pemahaman Standar Pelayanan PahamAja',
                'slug' => 'pemahaman-standar-pelayanan',
                'time_limit' => 20,
                'passing_score' => 70,
                'questions' => [
                    [
                        'text' => 'Apa nilai utama (Core Value) dari PahamAja dalam melayani pelanggan?',
                        'options' => [
                            ['text' => 'Empati dan Solutif', 'is_correct' => true],
                            ['text' => 'Kecepatan Tanpa Batas', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($quizzes as $qData) {
            try {
                // Individual try-catch to allow one quiz failure without killing the whole seeder
                DB::table('quizzes')->updateOrInsert(
                    ['slug' => $qData['slug']],
                    [
                        'title' => $qData['title'],
                        'time_limit' => $qData['time_limit'],
                        'passing_score' => $qData['passing_score'],
                        'updated_at' => now(),
                    ]
                );

                $quiz = Quiz::where('slug', $qData['slug'])->first();

                if ($quiz) {
                    foreach ($qData['questions'] as $questionData) {
                        try {
                            $question = Question::updateOrCreate(
                                ['quiz_id' => $quiz->id, 'text' => $questionData['text']],
                                ['quiz_id' => $quiz->id]
                            );

                            foreach ($questionData['options'] as $optionData) {
                                Option::updateOrCreate(
                                    ['question_id' => $question->id, 'text' => $optionData['text']],
                                    ['is_correct' => $optionData['is_correct']]
                                );
                            }
                        } catch (\Exception $qe) {
                            Log::warning("Skipped question in {$qData['slug']}: " . $qe->getMessage());
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to seed professional quiz {$qData['slug']}: " . $e->getMessage());
                continue; // IMPORTANT: Move to the next quiz if this one fails (e.g. ghost slug conflict)
            }
        }
    }
}
