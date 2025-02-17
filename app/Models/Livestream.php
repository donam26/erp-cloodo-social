<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Livestream extends Model
{
    use UuidTrait;
    protected $fillable = ['image', 'title', 'description'];
    
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'livestream_participants', 'livestream_id', 'user_id');
    }

    public function scopeLiveActive($query)
    {
        return $query->where('end_time', '=', null)
            ->where('host_id', '!=', auth()->user()->id);
    }
}
