<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->index('quiz_id', 'idx_participants_quiz_id');
            $table->index('employee_id', 'idx_participants_employee_id');
            $table->index('score', 'idx_participants_score');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->index('participant_id', 'idx_answers_participant_id');
            $table->index('question_id', 'idx_answers_question_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('quiz_id', 'idx_questions_quiz_id');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->index('question_id', 'idx_options_question_id');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex('idx_participants_quiz_id');
            $table->dropIndex('idx_participants_employee_id');
            $table->dropIndex('idx_participants_score');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('idx_answers_participant_id');
            $table->dropIndex('idx_answers_question_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_quiz_id');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropIndex('idx_options_question_id');
        });
    }
};
