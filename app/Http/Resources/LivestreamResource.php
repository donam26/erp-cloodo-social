<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestreamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'image' => $this->image,
            'title' => $this->title,
            'author' => new UserResource($this->host),
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'stream_url' => $this->stream_url,
        ];
    }
} 