<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use UuidTrait;

    protected $fillable = ['user_id', 'image'];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
