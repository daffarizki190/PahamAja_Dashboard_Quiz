<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nim', // Using NIM as Employee ID for compatibility
        'department',
        'position',
        'avatar',
        'status', // Active/Inactive
    ];

    protected $appends = ['avatar_url'];

    /**
     * Get the public URL for the employee's avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return avatar_url($this->avatar);
    }
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
