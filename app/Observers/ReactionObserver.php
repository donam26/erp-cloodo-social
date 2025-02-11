<?php

namespace App\Observers;

use App\Models\Reaction;
use Illuminate\Support\Facades\Storage;

class ReactionObserver
{
    public function creating(Reaction $reaction)
    {
        // Gán user_id khi tạo post
        $reaction->user_id = auth()->id();
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
        // Logic sau khi tạo post
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
