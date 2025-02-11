<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Lấy posts của user này
        $posts = $this->posts()
            ->with(['author', 'comments', 'reactions', 'images'])
            ->where('user_id', $this->id)
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
            'bio' => $this->bio,
            'gender' => $this->gender,
            'status' => $this->status,
            'posts' => [
                'total' => $this->posts()->where('user_id', $this->id)->count(),
                'items' => PostResource::collection($posts)
            ],
            'friends' => [
                'total' => $this->friends()->count(),
                'items' => UserResource::collection($friends)
            ],
            'created_at' => $this->created_at
        ];
    }
} 