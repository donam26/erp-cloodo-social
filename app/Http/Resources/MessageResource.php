<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'content' => $this->content,
            'conversation_id' => $this->conversation_id,
            'sender_id' => new UserResource($this->sender),
            'created_at' => $this->created_at,
        ];
    }
}
