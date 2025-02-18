<?php

namespace App\Notifications;

use App\Http\Resources\UserResource;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'comment' => $this->comment->content,
            'user' => UserResource::make($this->comment->user),
            'post_id' => $this->comment->post->uuid,
            'content' => $this->comment->content,
            'message' => "{$this->comment->user->name} đã bình luận về bài viết của bạn"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id, // ID của notification
            'type' => get_class($this),
            'data' => [
                'comment' => $this->comment->content,
                'user' => UserResource::make($this->comment->user),
                'post_id' => $this->comment->post->uuid,
                'content' => $this->comment->content,
                'message' => "{$this->comment->user->name} đã bình luận về bài viết của bạn"
            ],
            'created_at' => now()->toISOString()
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notifications');
    }
} 