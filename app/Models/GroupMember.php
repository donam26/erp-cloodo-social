<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use UuidTrait;
    protected $fillable = ['uuid', 'group_id', 'user_id', 'status'];
    
    protected $name = 'group_members';
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
