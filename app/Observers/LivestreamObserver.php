<?php

namespace App\Observers;

use App\Models\Livestream;
use App\Models\Reaction;
use Illuminate\Support\Facades\Storage;

class LivestreamObserver
{
    public function creating(Livestream $livestream)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $livestream->host_id = auth()->id();
        $livestream->start_time = now();
        $livestream->end_time = null;
    }

    public function deleting(Livestream $livestream)
    {
        // Xóa reaction khi xóa post
    }

    public function updating(Livestream $livestream)
    {
        // Logic khi update post
    }

    public function created(Livestream $livestream)
    {
        // Logic sau khi tạo post
    }

    public function updated(Livestream $livestream)
    {
        // Logic sau khi update post
    }

    public function deleted(Livestream $livestream)
    {
        // Logic sau khi xóa post
    }
}
