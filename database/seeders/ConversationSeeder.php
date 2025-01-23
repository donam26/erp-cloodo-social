<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Tạo 5 cuộc trò chuyện
        for ($i = 0; $i < 5; $i++) {
            $conversation = Conversation::create([
                'name' => 'Conversation ' . ($i + 1),
                'type' => 'group',
                'added_by' => $users->random()->id,
            ]);

            // Thêm 3-5 thành viên vào mỗi cuộc trò chuyện
            $memberCount = rand(3, 5);
            $randomUsers = $users->random($memberCount);

            foreach ($randomUsers as $user) {
                ConversationMember::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                ]);

                // Tạo 5 tin nhắn cho mỗi thành viên
                for ($j = 0; $j < 5; $j++) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $user->id,
                        'content' => 'Message ' . ($j + 1) . ' from ' . $user->name,
                        'type' => 'text',
                        'is_read' => false,
                    ]);
                }
            }
        }
    }
} 