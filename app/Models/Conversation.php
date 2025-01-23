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
}
