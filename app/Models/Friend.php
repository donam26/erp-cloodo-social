<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use UuidTrait;
    
    protected $fillable = ['user_id', 'friend_id', 'status'];
   
}
