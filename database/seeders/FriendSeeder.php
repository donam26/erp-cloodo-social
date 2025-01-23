<?php

namespace Database\Seeders;

use App\Models\Friend;
use App\Models\BlockList;
use App\Models\User;
use Illuminate\Database\Seeder;

class FriendSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Tạo 3-7 bạn bè cho mỗi user
            $friendCount = rand(3, 7);
            $potentialFriends = $users->except($user->id);
            $randomFriends = $potentialFriends->random($friendCount);

            foreach ($randomFriends as $friend) {
                // Kiểm tra xem đã có kết bạn chưa
                $existingFriend = Friend::where(function ($query) use ($user, $friend) {
                    $query->where('user_id', $user->id)
                        ->where('friend_id', $friend->id);
                })->orWhere(function ($query) use ($user, $friend) {
                    $query->where('user_id', $friend->id)
                        ->where('friend_id', $user->id);
                })->first();

                if (!$existingFriend) {
                    Friend::create([
                        'user_id' => $user->id,
                        'friend_id' => $friend->id,
                        'status' => ['pending', 'accepted'][rand(0, 1)],
                    ]);
                }
            }
          
        }
    }
} 