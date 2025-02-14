<?php

namespace App\Models;

use App\Builders\PostBuilder;
use App\Enums\FriendStatus;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use UuidTrait, Searchable;

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

    public function searchableAs()
    {
        return 'posts';
    }


    public function toSearchableArray()
    {
        $array = [
            'id' => $this->id,
            'content' => $this->content,
            'author' => $this->author->name,
            'created_at' => $this->created_at,
            'status' => $this->status
        ];

        // Thêm comments vào nội dung tìm kiếm
        $comments = $this->comments()->with('user')->get()->map(function ($comment) {
            return [
                'content' => $comment->content,
                'author' => $comment->user->name
            ];
        });
        $array['comments'] = $comments;

        return $array;
    }
}
