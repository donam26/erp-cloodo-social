<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailConversationResource;
use App\Http\Resources\LastMessageResource;
use App\Models\Conversation;
use App\Models\ConversationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = ConversationMember::with([
                'conversation.messages.sender',
                'conversation.participants',
                'conversation.messages' => function($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function($member) {
                $conversation = $member->conversation;
                $conversation->last_message = $conversation->messages->first();
                return $conversation;
            })
            ->sortByDesc(function($conversation) {
                return optional($conversation->last_message)->created_at;
            })
            ->values();

        return $this->successResponse(LastMessageResource::collection($conversations));
    }

    public function detail(Conversation $conversation)
    {
        $conversation->load(['messages.sender', 'participants']);
        return $this->successResponse(new DetailConversationResource($conversation));
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'type' => $request->type ?? 'private',
            'user_id' => Auth::id()
        ]);

        // Thêm người tạo vào nhóm
        ConversationMember::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id()
        ]);

        // Thêm người nhận vào nhóm
        if ($request->receiver_id) {
            ConversationMember::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->receiver_id
            ]);
        }

        return $this->successResponse(new LastMessageResource($conversation), 'Tạo cuộc trò chuyện thành công');
    }

    public function update(Request $request, Conversation $conversation)
    {
        $conversation->update($request->only(['type']));
        return $this->successResponse(new LastMessageResource($conversation), 'Cập nhật cuộc trò chuyện thành công');
    }

    public function delete(Conversation $conversation)
    {
        $conversation->delete();
        return $this->successResponse(null, 'Xóa cuộc trò chuyện thành công');
    }
}
