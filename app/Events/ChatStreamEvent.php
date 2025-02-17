<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\UserResource;

class ChatStreamEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('livestreams.' . $this->message->livestream_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->uuid,
                'content' => $this->message->message,
                'sender' => new UserResource($this->message->user),
                'created_at' => $this->message->created_at
            ]
        ];
    }
} 