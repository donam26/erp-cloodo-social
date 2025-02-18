<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use UuidTrait;

    protected $fillable = [
        'name',
        'description',
        'image',
        'status'
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function participated()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
