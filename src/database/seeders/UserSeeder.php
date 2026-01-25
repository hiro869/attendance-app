<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;

class UserSeeder extends Seeder
{
public function run()
{
    // 強制的に管理者ユーザーを作成
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    
    // 一般ユーザーが存在しない場合に作成
    if (!User::where('email', 'user@test.com')->exists()) {
        User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}
}
