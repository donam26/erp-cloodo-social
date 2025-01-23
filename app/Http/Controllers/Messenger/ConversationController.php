<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationMember;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = ConversationMember::with([
                'conversation.messages.sender',
                'conversation.messages' => function($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->where('user_id', auth()->id())
            ->get()
            ->map(function($member) {
                $conversation = $member->conversation;
                $lastMessage = optional($conversation->messages->first());
                $conversation->last_message = $lastMessage;
                return new ConversationResource($conversation);
            })
            ->sortByDesc(function($conversation) {
                return optional($conversation->last_message)->created_at;
            })
            ->values();

        return $this->successResponse(ConversationResource::collection($conversations));
    }

    public function detail(Conversation $conversation)
    {
        $messages = $conversation->load('messages');
        return $this->successResponse(MessageResource::collection($messages));
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create($request->all());
        return $this->successResponse(null, 'Tạo cuộc trò chuyện thành công');
    }

    public function update(Request $request, $id)
    {
        $conversation = Conversation::find($id);
        $conversation->update($request->all());
        return $this->successResponse(null, 'Cập nhật cuộc trò chuyện thành công');
    }

    public function delete($id)
    {
        $conversation = Conversation::find($id);
        $conversation->delete();
        return $this->successResponse(null, 'Xóa cuộc trò chuyện thành công');
    }
}
