<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả các user trong hệ thống
        $users = User::all();

        foreach ($users as $user) {
            // Tạo 5 bài post cho mỗi user

            for ($i = 0; $i < 5; $i++) {
                $userId = $user->id;       
                $post = Post::create([
                    'user_id' => $userId,
                    'content' => 'This is a sample post content for user ' . $userId . '.',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'status' => 'private',
                    'group_id' => null,
                ]);

                // Tạo 1-3 ảnh cho mỗi bài post
                $imageCount = rand(1, 3);
                for ($j = 0; $j < $imageCount; $j++) {
                    PostImage::create([
                        'post_id' => $post->id,
                        'image' => 'https://picsum.photos/800/600?random=' . rand(1, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
