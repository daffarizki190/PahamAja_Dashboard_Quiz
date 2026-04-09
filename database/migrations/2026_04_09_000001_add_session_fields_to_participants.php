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
        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('quiz_session_id')->nullable()->after('quiz_id')->constrained('quiz_sessions')->nullOnDelete();
            $table->dateTime('started_at')->nullable()->after('attempt');
            $table->dateTime('finished_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quiz_session_id');
            $table->dropColumn(['started_at', 'finished_at']);
        });
    }
};
