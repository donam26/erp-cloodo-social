<?php

namespace App\Observers;

use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\Auth;

class CommentObserver
{
    public function creating(Comment $comment)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $comment->user_id = Auth::id();
    }

    public function deleting(Comment $comment) {}

    public function updating(Comment $comment)
    {
    }

    public function created(Comment $comment)
    {
        if ($comment->post && $comment->post->author) {
            // if ($comment->post->author->id !== Auth::id()) {
                $comment->post->author->notify(new NewCommentNotification($comment));
            // }
        }
    }

    public function updated(Comment $comment)
    {
        // Logic sau khi update post
    }

    public function deleted(Comment $comment)
    {
        // Logic sau khi xóa post
    }
}
