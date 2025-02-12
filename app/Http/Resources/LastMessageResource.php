<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LastMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'type' => $this->type,
            'last_message' => $this->last_message ? [
                'id' => $this->last_message->uuid,
                'content' => $this->last_message->content,
                'sender' => new UserResource($this->last_message->sender),
                'created_at' => $this->last_message->created_at
            ] : null,
            'participants' => UserResource::collection($this->participants),
            'created_at' => $this->created_at
        ];
    }
}
