<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Tạo 5 nhóm
        for ($i = 0; $i < 5; $i++) {
            // Chọn ngẫu nhiên một user làm admin của nhóm
            $adminUser = $users->random();
            
            $group = Group::create([
                'name' => 'Group ' . ($i + 1),
                'description' => 'This is a sample group ' . ($i + 1),
                'image' => 'https://picsum.photos/200/200?random=' . rand(1, 1000),
                'image_background' => 'https://picsum.photos/800/300?random=' . rand(1, 1000),
                'status' => ['public', 'private'][rand(0, 1)],
            ]);

            // Thêm 5-10 thành viên vào nhóm
            $memberCount = rand(5, 10);
            $randomUsers = $users->except($adminUser->id)->random($memberCount);

            foreach ($randomUsers as $user) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'status' => ['pending', 'accepted'][rand(0, 1)]
                ]);
            }
        }
    }
} 