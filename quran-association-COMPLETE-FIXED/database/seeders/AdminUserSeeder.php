<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف المستخدم الإداري القديم إن وجد
        User::where('email', 'admin@quran.com')->delete();
        
        // إنشاء المستخدم الإداري الجديد
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@quran.com',
            'password' => Hash::make('password'),
            'phone' => '0501234000',
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true
        ]);
        
        $this->command->info('✅ تم إنشاء حساب المدير بنجاح');
        $this->command->info('📧 البريد الإلكتروني: admin@quran.com');
        $this->command->info('🔑 كلمة المرور: password');
    }
}

