<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\AiGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class AiInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_insight_returns_structured_payload(): void
    {
        $quiz = Quiz::create([
            'title' => 'Insight Quiz',
            'slug' => Str::slug('Insight Quiz').'-'.Str::random(5),
            'time_limit' => 10,
            'passing_score' => 70,
        ]);

        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'text' => 'Question 1?',
        ]);

        $o1a = Option::create([
            'question_id' => $q1->id,
            'text' => 'A',
            'is_correct' => 1,
        ]);

        $o1b = Option::create([
            'question_id' => $q1->id,
            'text' => 'B',
            'is_correct' => 0,
        ]);

        $participant = Participant::create([
            'quiz_id' => $quiz->id,
            'employee_id' => null,
            'name' => 'Tester',
            'nim' => 'T-001',
            'score' => 100,
            'attempt' => 1,
        ]);

        Answer::create([
            'participant_id' => $participant->id,
            'question_id' => $q1->id,
            'option_id' => $o1a->id,
        ]);

        $mock = Mockery::mock(AiGeneratorService::class);
        $mock->shouldReceive('generateInsight')->andReturn(implode("\n", [
            'Ringkasan: Ringkas.',
            'Diagnosis Utama: Diagnosa.',
            'Area Perhatian: A; B; C',
            'Temuan Data:',
            '- Temuan 1',
            '- Temuan 2',
            '- Temuan 3',
            'Rekomendasi Peserta:',
            '- Aksi — Alasan: X — Cara: Y',
            '- Aksi — Alasan: X — Cara: Y',
            '- Aksi — Alasan: X — Cara: Y',
            'Rekomendasi Trainer/Materi:',
            '- Aksi — Dampak: X — Cara: Y',
            '- Aksi — Dampak: X — Cara: Y',
            'Rekomendasi Assessment/Soal:',
            '- Aksi — Target: X — Cara: Y',
            '- Aksi — Target: X — Cara: Y',
            'Rencana 7 Hari:',
            'Hari 1-2: ...',
            'Hari 3-5: ...',
            'Hari 6-7: ...',
            'AKHIR_INSIGHT',
        ]));

        $this->app->instance(AiGeneratorService::class, $mock);

        $response = $this
            ->withSession(['admin.authenticated' => true])
            ->postJson(route('admin.quiz.ai-insights', $quiz->slug));

        $response->assertOk();
        $response->assertJsonStructure([
            'insight',
            'meta' => [
                'quiz' => ['title', 'passing_score'],
                'participants' => ['total', 'finished', 'completion_rate', 'passed', 'failed', 'avg_score', 'highest', 'lowest', 'distribution'],
                'questions' => ['hardest', 'easiest'],
            ],
        ]);
    }
}
