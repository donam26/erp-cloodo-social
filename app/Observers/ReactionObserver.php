<?php

namespace App\Observers;

use App\Models\Reaction;
use App\Notifications\NewLikeNotification;
use Illuminate\Support\Facades\Auth;

class ReactionObserver
{
    public function creating(Reaction $reaction)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $reaction->user_id = Auth::id();
    }

    public function deleting(Reaction $reaction)
    {
        // Xóa reaction khi xóa post
    }

    public function updating(Reaction $reaction)
    {
        // Logic khi update post
    }

    public function created(Reaction $reaction)
    {
        if ($reaction->post && $reaction->post->author) {
            // if ($reaction->post->author->id !== Auth::id()) {
            $reaction->post->author->notify(new NewLikeNotification($reaction));
            // }
        }
    }

    public function updated(Reaction $reaction)
    {
        // Logic sau khi update post
    }

    public function deleted(Reaction $reaction)
    {
        // Logic sau khi xóa post
    }
}
