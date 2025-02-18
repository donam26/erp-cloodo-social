<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailConversationResource;
use App\Http\Resources\LastMessageResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\User;
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
            ->latest()
            ->limit($limit)
            ->get()
            ->sortBy('created_at')
            ->values();

        return $this->successResponse([
            'conversation' => new DetailConversationResource($conversation),
            'messages' => [
                'items' => MessageResource::collection($messages),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $participants = $request->participants;
        $type = $request->type ?? 'private';
        $currentUserId = Auth::id();

        if ($type === 'private' && count($participants) === 1) {
            $participant = User::where('uuid', $participants[0])->first();
            
            // Tìm cuộc trò chuyện private giữa 2 người
            $existingConversation = Conversation::whereHas('members', function($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            })->whereHas('members', function($query) use ($participant) {
                $query->where('user_id', $participant->id);
            })->where('type', 'private')
            ->first();

            if ($existingConversation) {
                return $this->successResponse(
                    new LastMessageResource($existingConversation), 
                    'Đã tìm thấy cuộc trò chuyện'
                );
            }
        }

        $conversation = Conversation::create([
            'name' => $request->name ?? null,
            'type' => $type,
            'added_by' => $currentUserId
        ]);

        // Thêm người tạo vào nhóm
        ConversationMember::create([
            'conversation_id' => $conversation->id,
            'user_id' => $currentUserId
        ]);

        // Thêm người nhận vào nhóm
        foreach ($participants as $participant) {
            $user = User::where('uuid', $participant)->first();
            ConversationMember::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id
            ]);
        }

        return $this->successResponse(
            new LastMessageResource($conversation), 
            'Tạo cuộc trò chuyện thành công'
        );
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
