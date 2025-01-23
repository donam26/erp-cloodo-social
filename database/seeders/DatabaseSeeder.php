<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FriendSeeder::class,
            GroupSeeder::class,
            PostSeeder::class,
            ConversationSeeder::class,
        ]);
    }
}
