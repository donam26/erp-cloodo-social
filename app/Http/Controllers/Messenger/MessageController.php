<?php

namespace App\Http\Controllers\Messenger;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Messenger\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $message = Message::create([
            'content' => $data['content'],
            'conversation_id' => $data['conversation_id'],
        ]);

        broadcast(new MessageSent($message))->toOthers();
        
        return $this->successResponse(new MessageResource($message), 'Tạo tin nhắn thành công');
    }

    public function delete(Message $message)
    {
        $message->delete();
        return $this->successResponse(null, 'Xóa tin nhắn thành công');
    }
}
