<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'employee_id',
        'name',
        'nim',
        'score',
        'attempt',
    ];

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
