<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndGameResult implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $gameId;
    public $accepted;
    public $opponentScore;
    public $userScore;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, int $gameId, bool $accepted, int $userScore, int $opponentScore)
    {
        $this->user = $user;
        $this->gameId = $gameId;
        $this->accepted = $accepted;
        $this->opponentScore = $opponentScore;
        $this->userScore = $userScore;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('end-game-result.' . $this->gameId);
    }
}
