<?php

namespace App\Observers;

use App\Models\Message;

class MessengerObserver
{
    public function creating(Message $message)
    {
        // $message->sender_id = auth()->user()->id;
    }
}
