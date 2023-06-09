<?php

namespace App\Events;

use App\Models\Board;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $board;
    public $gameId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Board $board, int $gameId)
    {
        $this->user = $user;
        $this->board = $board;
        $this->gameId = $gameId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('board.' . $this->gameId);
    }
}
