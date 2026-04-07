<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nim', // Using NIM as Employee ID for compatibility
        'department',
        'position',
        'status', // Active/Inactive
    ];

    /**
     * Get all of the participant entries for the Employee.
     * This tracks the employee's history across different quizzes.
     */
    public function participations(): HasMany
    {
        return $this->hasMany(Participant::class, 'nim', 'nim');
    }

    /**
     * Calculate average score across all completed quizzes.
     */
    public function getAverageScoreAttribute()
    {
        return $this->participations()->whereNotNull('score')->avg('score') ?? 0;
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'employee_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
