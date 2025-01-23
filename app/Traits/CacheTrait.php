<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    public function cache($key, $value, $minutes = 10)
    {
        return Cache::put($key, $value, $minutes);
    }

    public function getCache($key)
    {
        return Cache::get($key);
    }

    public function forgetCache($key)
    {
        return Cache::forget($key);
    }
}