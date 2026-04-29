<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'event_type',
        'payload',
        'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Get the participant that owns the log.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
