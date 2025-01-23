<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use UuidTrait;
    
    protected $fillable = ['post_id', 'image'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
