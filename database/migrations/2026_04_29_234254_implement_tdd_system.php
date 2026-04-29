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
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('device_info')->nullable();
            
            if (Schema::hasColumn('participants', 'signature')) {
                $table->dropColumn('signature');
            }
        });

        Schema::create('participant_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // start, next, prev, answer, blur, focus, submit
            $table->json('payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['participant_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_logs');
        
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent', 'device_info']);
            $table->string('signature')->nullable();
        });
    }
};
