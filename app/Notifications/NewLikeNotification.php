<?php

namespace App\Notifications;

use App\Http\Resources\UserResource;
use App\Models\Reaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reaction;

    public function __construct(Reaction $reaction)
    {
        $this->reaction = $reaction;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'reaction' => $this->reaction->content,
            'user' => UserResource::make($this->reaction->user),
            'post_id' => $this->reaction->post->uuid,
            'content' => $this->reaction->content,
            'message' => "{$this->reaction->user->name} đã thích bài viết của bạn"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'reaction' => $this->reaction->content,
            'user' => UserResource::make($this->reaction->user),
            'post_id' => $this->reaction->post->uuid,
            'content' => $this->reaction->content,
            'message' => "{$this->reaction->user->name} đã thích bài viết của bạn"
        ]);
    }
} 