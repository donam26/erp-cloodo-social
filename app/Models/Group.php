<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use UuidTrait;

    protected $fillable = ['name', 'description', 'status', 'image'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function participated()
    {
        return $this->hasMany(GroupMember::class);
    }
}
