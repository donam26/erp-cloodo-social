<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \Illuminate\Support\Collection) {
            // Nếu là collection (groupBy user_id), format theo user
            return [
                'user' => new UserResource($this->first()->author),
                'stories' => $this->map(function ($story) {
                    return [
                        'id' => $story->uuid,
                        'background' => $story->background,
                        'text' => $story->text,
                        'created_at' => $story->created_at,
                        'expired_at' => $story->expired_at
                    ];
                })
            ];
        }

        // Nếu là single story
        return [
            'id' => $this->uuid,
            'author' => new UserResource($this->author),
            'background' => $this->background,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'expired_at' => $this->expired_at
        ];
    }
}
