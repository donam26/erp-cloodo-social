<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();
        
        // Lấy posts của user này
        $posts = $this->posts()
            ->with(['author', 'comments', 'reactions', 'images'])
            ->where('user_id', $this->id)
            ->where(function($query) use ($request) {
                // Lấy friend_status từ response của controller
                $friendStatus = $request->get('friend_status');
                $isFriend = $friendStatus === 'accepted';
                
                if ($isFriend) {
                    // Nếu là bạn bè thì xem được bài public và friends
                    $query->whereIn('status', ['public', 'friends']);
                } else {
                    // Nếu không phải bạn bè thì chỉ xem được bài public
                    $query->where('status', 'public');
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy friends của user này
        $friends = $this->friends()
            ->where(function($query) {
                $query->where('friends.user_id', $this->id)
                    ->orWhere('friends.friend_id', $this->id);
            })
            ->limit(6)
            ->get();

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'image' => $this->image ?? url('/images/avatar.jpg'),
            'image_background' => $this->image_background,
            'friend_status' => $request->get('friend_status'),  
            'bio' => $this->bio,
            'gender' => $this->gender,
            'status' => $this->status,
            'posts' => [
                'total' => $this->posts()
                    ->where('user_id', $this->id)
                    ->where(function($query) use ($request) {
                        $friendStatus = $request->get('friend_status');
                        $friendStatus = $request->get('friend_status');
                        $isFriend = $friendStatus === 'accepted';
                        
                        if ($isFriend) {
                            $query->whereIn('status', ['public', 'friends']);
                        } else {
                            $query->where('status', 'public');
                        }
                    })
                    ->count(),
                'items' => PostResource::collection($posts)
            ],
            'friends' => [
                'total' => $this->friends()->count(),
                'items' => UserResource::collection($friends)
            ],
            'created_at' => $this->created_at,
        ];
    }
} 