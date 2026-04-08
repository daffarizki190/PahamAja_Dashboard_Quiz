<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminQuizUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_quiz_and_questions_and_existing_participants_are_removed(): void
    {
        $quiz = Quiz::create([
            'title' => 'Original Quiz',
            'slug' => Str::slug('Original Quiz').'-'.Str::random(5),
            'time_limit' => 30,
            'passing_score' => 70,
        ]);

        $oldQuestion = Question::create([
            'quiz_id' => $quiz->id,
            'text' => 'Old question?',
        ]);

        $oldOptionA = Option::create([
            'question_id' => $oldQuestion->id,
            'text' => 'Old A',
            'is_correct' => 1,
        ]);

        $oldOptionB = Option::create([
            'question_id' => $oldQuestion->id,
            'text' => 'Old B',
            'is_correct' => 0,
        ]);

        $participant = Participant::create([
            'quiz_id' => $quiz->id,
            'employee_id' => null,
            'name' => 'Tester',
            'nim' => '123',
            'score' => 100,
            'attempt' => 1,
        ]);

        $answer = Answer::create([
            'participant_id' => $participant->id,
            'question_id' => $oldQuestion->id,
            'option_id' => $oldOptionA->id,
        ]);

        $payload = [
            'title' => 'Updated Quiz',
            'time_limit' => 45,
            'passing_score' => 80,
            'questions' => [
                [
                    'text' => 'New question 1?',
                    'options' => [
                        ['text' => 'A'],
                        ['text' => 'B'],
                        ['text' => 'C'],
                        ['text' => 'D'],
                    ],
                    'correct_option' => 2,
                ],
                [
                    'text' => 'New question 2?',
                    'options' => [
                        ['text' => 'A2'],
                        ['text' => 'B2'],
                        ['text' => 'C2'],
                        ['text' => 'D2'],
                    ],
                    'correct_option' => 0,
                ],
            ],
        ];

        $this->withoutExceptionHandling();

        $token = 'test-csrf-token';

        $response = $this
            ->withSession(['_token' => $token])
            ->withoutMiddleware([\App\Http\Middleware\AdminAuth::class])
            ->patch(route('admin.quizzes.update', $quiz->slug), $payload + ['_token' => $token]);

        $response->assertRedirect(route('admin.quizzes.show', $quiz->slug));

        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'Updated Quiz',
            'time_limit' => 45,
            'passing_score' => 80,
        ]);

        $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
        $this->assertDatabaseMissing('answers', ['id' => $answer->id]);

        $this->assertDatabaseMissing('questions', ['id' => $oldQuestion->id]);
        $this->assertDatabaseMissing('options', ['id' => $oldOptionA->id]);
        $this->assertDatabaseMissing('options', ['id' => $oldOptionB->id]);

        $quiz->refresh()->load('questions.options');
        $this->assertCount(2, $quiz->questions);

        $question1 = $quiz->questions->firstWhere('text', 'New question 1?');
        $this->assertNotNull($question1);
        $this->assertCount(4, $question1->options);

        $optionsByText = $question1->options->keyBy('text');
        $this->assertTrue($optionsByText->get('C')?->is_correct);
        $this->assertFalse($optionsByText->get('A')?->is_correct);
    }
}
