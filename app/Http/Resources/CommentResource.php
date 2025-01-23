<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $author = User::where('id', $this->user_id)->first();
        return [
            'id' => $this->uuid,
            'author' => new UserResource($author),
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}
