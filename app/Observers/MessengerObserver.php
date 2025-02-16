<?php

namespace App\Observers;

use App\Models\Message;

class MessengerObserver
{
    public function creating(Message $message)
    {
        if (app()->runningInConsole()) {
            return; // Không thực hiện hành động gì khi đang chạy seeder
        }
        $message->sender_id = auth()->user()->id;
    }
}
