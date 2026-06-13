<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'passing_score' => 70,
    ];

    protected $fillable = [
        'title',
        'slug',
        'time_limit',
        'passing_score',
        'essay_grading_method',
        'is_public',
        'status',
    ];

    protected $casts = [
        // removed is_public due to pgsql pgbouncer emulated prepares issue
    ];

    /**
     * Set the is_public attribute to a string for Postgres.
     */
    public function setIsPublicAttribute($value)
    {
        $this->attributes['is_public'] = $value ? 'true' : 'false';
    }

    /**
     * Get the is_public attribute as a boolean.
     */
    public function getIsPublicAttribute($value)
    {
        return $value === 'true' || $value === true || $value === 1 || $value === '1' || $value === 't';
    }

    /**
     * Get the count of participants — uses pre-loaded value if available (avoids N+1).
     */
    public function getParticipantsCountAttribute(): int
    {
        if (array_key_exists('participants_count', $this->attributes)) {
            return (int) $this->attributes['participants_count'];
        }

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

    /**
     * Get all of the sessions for the Quiz
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(QuizSession::class);
    }
}
