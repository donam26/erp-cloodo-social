<?php

namespace App\Observers;

use App\Events\ChatStreamEvent;
use App\Models\ChatStream;
use App\Models\Livestream;
use Illuminate\Support\Facades\Storage;

class ChatStreamObserver
{
    public function creating(ChatStream $chatStream)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $chatStream->user_id = auth()->id();
    }

    public function deleting(ChatStream $chatStream)
    {
        // Xóa reaction khi xóa post
    }

    public function updating(ChatStream $chatStream)
    {
        // Logic khi update post
    }

    public function created(ChatStream $chatStream)
    {
        event(new ChatStreamEvent($chatStream));
    }

    public function updated(ChatStream $chatStream)
    {
        // Logic sau khi update post
    }

    public function deleted(ChatStream $chatStream)
    {
        // Logic sau khi xóa post
    }
}
