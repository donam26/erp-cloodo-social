<?php

namespace App\Models;

use App\Builders\PostBuilder;
use App\Enums\FriendStatus;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use UuidTrait;

    protected $fillable = [
        'content',
        'user_id',
        'status',
        'group_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    public function newEloquentBuilder($query): PostBuilder
    {
        return new PostBuilder($query);
    }

 
}
