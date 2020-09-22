<?php

namespace App\Events;

use App\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }
}
