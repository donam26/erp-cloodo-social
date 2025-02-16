<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostImage extends Model
{
    use UuidTrait;
    
    protected $fillable = [
        'post_id',
        'image'
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('s3')->url($this->image);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
