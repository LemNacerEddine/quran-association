<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            OrganizedTestDataSeeder::class,
        ]);
        
        $this->command->info('🎉 تم إنشاء جميع البيانات التجريبية بنجاح!');
        $this->command->info('');
        $this->command->info('📋 بيانات الدخول:');
        $this->command->info('');
        $this->command->info('🔐 الإدارة:');
        $this->command->info('   البريد الإلكتروني: admin@quran.com');
        $this->command->info('   كلمة المرور: password');
        $this->command->info('');
        $this->command->info('👨‍🏫 المعلم:');
        $this->command->info('   الهاتف: 0501234888');
        $this->command->info('   كود الدخول: 4888');
        $this->command->info('');
        $this->command->info('👨‍👩‍👧‍👦 أولياء الأمور:');
        $this->command->info('   أحمد عبدالله: 0501234567 / 4567');
        $this->command->info('   محمد حسن: 0501234568 / 4568');
        $this->command->info('   علي أحمد: 0501234569 / 4569');
        $this->command->info('   سالم محمد: 0501234570 / 4570');
        $this->command->info('   إبراهيم يوسف: 0501234571 / 4571');
        $this->command->info('');
        $this->command->info('📊 البيانات المنشأة:');
        $this->command->info('   - حلقة واحدة مع 5 طلاب');
        $this->command->info('   - 36 جلسة (3 أشهر)');
        $this->command->info('   - بيانات حضور ونقاط كاملة');
        $this->command->info('   - إحصائيات واقعية ومتنوعة');
    }
}

