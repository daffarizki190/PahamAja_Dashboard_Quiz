<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerAttacked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quizId;
    public $attackerName;
    public $targetParticipantId;
    public $attackType;

    /**
     * Create a new event instance.
     */
    public function __construct($quizId, $attackerName, $targetParticipantId, $attackType)
    {
        $this->quizId = $quizId;
        $this->attackerName = $attackerName;
        $this->targetParticipantId = $targetParticipantId;
        $this->attackType = $attackType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // Using the same channel format as QuizUpdated for simplicity and compatibility
        return [
            new PrivateChannel('quiz.' . $this->quizId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'attacker_name' => $this->attackerName,
            'target_participant_id' => $this->targetParticipantId,
            'attack_type' => $this->attackType,
        ];
    }
}
