<?php

namespace App\Http\Controllers\Messenger;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Messenger\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        
        $conversation = Conversation::where('uuid', $data['conversationId'])
            ->first();

        if (!$conversation) {
            return $this->errorResponse('Không tìm thấy cuộc trò chuyện', 404);
        }

        $message = Message::create([
            'content' => $data['content'],
            'conversation_id' => $conversation->id,
            'type' => 'text',
            'sender_id' => auth()->id()
        ]);

        // Eager load sender và conversation để tránh N+1 query
        $message->load(['sender', 'conversation']);

        broadcast(new MessageSent($message))->toOthers();
        return $this->successResponse(new MessageResource($message), 'Tạo tin nhắn thành công');
    }

    public function delete(Message $message)
    {
        $message->delete();
        return $this->successResponse(null, 'Xóa tin nhắn thành công');
    }
}
