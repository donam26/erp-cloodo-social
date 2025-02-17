<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

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
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'user_id' => $this->comment->user_id,
            'content' => $this->comment->content,
            'message' => "{$this->comment->user->name} đã bình luận về bài viết của bạn"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'user_id' => $this->comment->user_id,
            'content' => $this->comment->content,
            'message' => "{$this->comment->user->name} đã bình luận về bài viết của bạn"
        ]);
    }
} 