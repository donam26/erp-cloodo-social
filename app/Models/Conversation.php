<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    use UuidTrait;
    protected $fillable = ['user_id', 'type', 'receiver_id', 'added_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_members', 'conversation_id', 'user_id');
    }

    public function members()
    {
        return $this->hasMany(ConversationMember::class);
    }
}
