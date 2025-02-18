<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\ReactionType;
use App\Models\Group;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        if ($this->group_id) {
            $group = Group::find($this->group_id);
        } else {
            $group = null;
        }
        return [
            'id' => $this->uuid,
            'content' => $this->content,
            'group' => $group ? new GroupResource($group) : null,
            'author' => new UserResource($this->author),
            'status' => $this->status,
            'comments' => CommentResource::collection($this->comments()->with('user')->latest()->get()),
            'total_comments' => $this->comments()->count(),
            'reactions' => [
                'total' => $this->reactions()->count(),
                'current_user_reacted' => $this->reactions()->where('user_id', $user->id)->exists(),
                'types' => [
                    'like' => $this->reactions()->where('type', ReactionType::Like)->count(),
                    'love' => $this->reactions()->where('type', ReactionType::Love)->count(),
                    'haha' => $this->reactions()->where('type', ReactionType::Haha)->count(),
                    'wow' => $this->reactions()->where('type', ReactionType::Wow)->count(),
                    'sad' => $this->reactions()->where('type', ReactionType::Sad)->count(),
                    'angry' => $this->reactions()->where('type', ReactionType::Angry)->count(),
                ]
            ],
            'images' => $this->images,
            'created_at' => $this->created_at,
        ];
    }
}
