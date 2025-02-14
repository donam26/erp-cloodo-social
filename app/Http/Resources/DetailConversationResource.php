<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailConversationResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'participants' => UserResource::collection($this->participants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
