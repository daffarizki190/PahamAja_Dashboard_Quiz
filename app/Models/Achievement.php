<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon', 'condition', 'threshold'];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
