<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'quiz_session_id',
        'employee_id',
        'name',
        'nim',
        'score',
        'attempt',
        'started_at',
        'finished_at',
        'is_assigned',
        'status',
        'ip_address',
        'user_agent',
        'device_info',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_assigned' => 'boolean',
        'device_info' => 'array',
    ];

    /**
     * Get the logs associated with this participant.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ParticipantLog::class);
    }

    /**
     * Get the session associated with this participation.
     */
    public function quizSession(): BelongsTo
    {
        return $this->belongsTo(QuizSession::class);
    }

    /**
     * Get the master employee record for this participant.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the quiz that owns the Participant
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get all of the answers for the Participant
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
