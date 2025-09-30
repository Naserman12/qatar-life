<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف جميع المستخدمين القدامى
        // User::truncate();
        
        // إنشاء مستخدم مدير ثابت (admin)
        User::create([
            'name' => '؛حافظ موسى',
            'email' => 'na615448@gmail.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('Hafid053'),
            'remember_token' => Str::random(10),
        ]);
        
        // NIXPACKS_BUILD_CMD=php artisan db:seed --class=Database\\Seeders\\UsersTableSeeder
        // إنشاء 20 مستخدمًا عاديًا باستخدام الـ factory
        User::factory()->count(20)->create();
    
    }
}
