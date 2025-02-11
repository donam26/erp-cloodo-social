<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
    public function creating(Post $post)
    {
        // Gán user_id khi tạo post
        $post->user_id = auth()->id();
    }

    public function deleting(Post $post)
    {
        // Xóa images khi xóa post
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();
        }
    }

    public function updating(Post $post)
    {
        // Logic khi update post
    }

    public function created(Post $post)
    {
        // Logic sau khi tạo post
    }

    public function updated(Post $post)
    {
        // Logic sau khi update post
    }

    public function deleted(Post $post)
    {
        // Logic sau khi xóa post
    }
}
