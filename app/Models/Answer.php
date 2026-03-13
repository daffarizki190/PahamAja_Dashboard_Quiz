<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'question_id',
        'option_id',
    ];

    /**
     * Get the participant that owns the Answer
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the question that owns the Answer
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the option that the Answer is associated with
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
