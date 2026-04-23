<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizJoinTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_see_join_form()
    {
        $quiz = Quiz::factory()->create([
            'slug' => 'test-quiz',
            'title' => 'Test Quiz'
        ]);

        $response = $this->get(route('quiz.join', $quiz->slug));

        $response->assertStatus(200);
    }

    public function test_employee_can_submit_join_form()
    {
        $quiz = Quiz::factory()->create([
            'slug' => 'test-quiz',
        ]);
        
        $employee = Employee::factory()->create([
            'status' => 'Active',
            'nim' => '12345678'
        ]);

        $response = $this->post(route('quiz.join.process', $quiz->slug), [
            'nim' => '12345678'
        ]);

        $response->assertRedirect(route('quiz.confirm-profile', [$quiz->slug, $employee->id]));
    }
}
