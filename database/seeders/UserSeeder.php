<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@cloodo.com',
            'password' => Hash::make('261102'),
        ]);

        // Tạo thêm 10 user mẫu
        User::factory(10)->create();
    }
} 