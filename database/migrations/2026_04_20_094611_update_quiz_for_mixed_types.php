<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('essay_grading_method')->default('manual')->comment('manual, ai');
        });

        // 2. Update questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type')->default('mcq')->comment('mcq, essay');
            $table->text('ideal_answer')->nullable();
        });

        // 3. Update answers table
        Schema::table('answers', function (Blueprint $table) {
            $table->text('essay_answer')->nullable();
            $table->float('score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->unsignedBigInteger('option_id')->nullable()->change();
        });

        // 4. Update participants table
        Schema::table('participants', function (Blueprint $table) {
            $table->string('status')->default('completed')->comment('in_progress, pending_review, completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn(['essay_answer', 'score', 'ai_feedback']);
            $table->unsignedBigInteger('option_id')->nullable(false)->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'ideal_answer']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('essay_grading_method');
        });
    }
};
