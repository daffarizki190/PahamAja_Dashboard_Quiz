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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('passing_score')->default(70)->after('time_limit');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->integer('cheat_attempts')->default(0)->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('passing_score');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('cheat_attempts');
        });
    }
};
