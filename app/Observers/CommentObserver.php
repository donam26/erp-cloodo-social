<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Storage;

class CommentObserver
{
    public function creating(Comment $comment)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $comment->user_id = auth()->id();
    }

    public function deleting(Comment $comment) {}

    public function updating(Comment $comment)
    {
        // Logic khi update post
    }

    public function created(Comment $comment)
    {
        // Logic sau khi tạo post
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
