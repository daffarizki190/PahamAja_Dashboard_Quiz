<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_quiz_pdf(): void
    {
        $quiz = Quiz::create([
            'title' => 'PDF Export Quiz',
            'slug' => Str::slug('PDF Export Quiz').'-'.Str::random(5),
            'time_limit' => 10,
            'passing_score' => 70,
        ]);

        Participant::create([
            'quiz_id' => $quiz->id,
            'employee_id' => null,
            'name' => 'Tester',
            'nim' => 'T-001',
            'score' => 80,
            'attempt' => 1,
        ]);

        $response = $this
            ->withSession(['admin.authenticated' => true])
            ->get(route('admin.quiz.export-pdf', $quiz->slug));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }
}
