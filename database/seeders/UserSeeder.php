<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'pageination' => 1,
            'name' => 'DEV SYNOEUN',
            'department_code' => 'DEP001',
            'email' => 'developer251121@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'), // Always hash passwords!
            'user_code' => 'U123456789',
            'user_type' => 'admin',
            'role' => 'administrator',
            'permission' => 'full',
            'phone' => '1234567890',
            'session_token' => Str::random(100),
            'remember_token' => Str::random(10),
        ]);
    }
}
