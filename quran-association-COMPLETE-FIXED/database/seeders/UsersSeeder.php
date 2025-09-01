<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم مدير
        User::create([
            'name' => 'مدير النظام',
            'phone' => '0501234567',
            'email' => 'admin@quran-association.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // إنشاء أولياء أمور
        $parents = [
            [
                'name' => 'أحمد محمد العلي',
                'phone' => '0501111111',
                'email' => 'ahmed.ali@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'فاطمة عبدالله السالم',
                'phone' => '0502222222',
                'email' => 'fatima.salem@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'محمد عبدالرحمن الخالد',
                'phone' => '0503333333',
                'email' => 'mohammed.khaled@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'عائشة سعد المطيري',
                'phone' => '0504444444',
                'email' => 'aisha.mutairi@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'عبدالله أحمد الزهراني',
                'phone' => '0505555555',
                'email' => 'abdullah.zahrani@example.com',
                'password' => Hash::make('123456'),
            ],
        ];

        foreach ($parents as $parent) {
            User::create([
                'name' => $parent['name'],
                'phone' => $parent['phone'],
                'email' => $parent['email'],
                'password' => $parent['password'],
                'role' => 'parent',
                'is_active' => true,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]);
        }
    }
}

