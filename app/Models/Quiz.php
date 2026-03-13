<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $attributes = [
        'passing_score' => 70,
    ];

    protected $fillable = [
        'title',
        'slug',
        'time_limit',
        'passing_score',
    ];

    protected $appends = ['participants_count'];

    /**
     * Get the count of participants for the quiz.
     */
    public function getParticipantsCountAttribute(): int
    {
        return $this->participants()->count();
    }

    /**
     * Get all of the questions for the Quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get all of the participants for the Quiz
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
}
