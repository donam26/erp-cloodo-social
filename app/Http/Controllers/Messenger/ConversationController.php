<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailConversationResource;
use App\Http\Resources\LastMessageResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = ConversationMember::with([
                'conversation.participants',
                'conversation.messages' => function($query) {
                    $query->whereIn('id', function($subQuery) {
                        $subQuery->select(\DB::raw('MAX(id)'))
                            ->from('messages')
                            ->groupBy('conversation_id');
                    })->with('sender');
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

    public function detail(Conversation $conversation, Request $request)
    {
        $limit = $request->input('limit', 20);
        
        $conversation->load(['participants']);
        $messages = $conversation->messages()
            ->with('sender')
            ->paginate($limit);

        return $this->successResponse([
            'conversation' => new DetailConversationResource($conversation),
            'messages' => [
                'total' => $messages->total(),
                'items' => MessageResource::collection($messages),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage()
            ]
        ]);
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
