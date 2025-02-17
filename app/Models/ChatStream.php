<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class ChatStream extends Model
{
    use UuidTrait;

    protected $fillable = ['livestream_id', 'user_id', 'message'];

    public function livestream()
    {
        return $this->belongsTo(Livestream::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}