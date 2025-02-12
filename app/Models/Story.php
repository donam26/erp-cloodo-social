<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use UuidTrait;

    protected $fillable = [
        'user_id',
        'background',
        'text',
        'expired_at'
    ];

    protected $casts = [
        'background' => 'json',
        'text' => 'json',
        'expired_at' => 'datetime'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope để lấy stories chưa hết hạn
    public function scopeActive($query)
    {
        return $query->where('expired_at', '>', now());
    }
}
