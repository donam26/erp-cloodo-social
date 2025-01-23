<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'access_token' => $this->resource['access_token'],
            'type' => 'bearer',
            'expires_in' => $this->resource['expires_in'],
            'user' => new UserResource($this->resource['user']),
        ];
    }
}
