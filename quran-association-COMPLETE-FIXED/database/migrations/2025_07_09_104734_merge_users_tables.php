<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة الحقول الجديدة لجدول users الموجود
        Schema::table('users', function (Blueprint $table) {
            // إضافة الحقول المفقودة من users_new
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->unique()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'parent'])->default('parent')->after('password');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('phone_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->string('fcm_token')->nullable()->after('last_login_at');
            }
            
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('fcm_token');
            }
        });

        // إضافة الفهارس
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', ['phone'])) {
                $table->index('phone');
            }
            if (!Schema::hasIndex('users', ['role'])) {
                $table->index('role');
            }
            if (!Schema::hasIndex('users', ['is_active'])) {
                $table->index('is_active');
            }
        });

        // نسخ البيانات من users_new إلى users إذا كانت موجودة
        if (Schema::hasTable('users_new')) {
            $usersNew = DB::table('users_new')->get();
            foreach ($usersNew as $user) {
                // التحقق من عدم وجود المستخدم مسبقاً
                $existingUser = DB::table('users')->where('email', $user->email)->first();
                if (!$existingUser) {
                    DB::table('users')->insert([
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'password' => $user->password,
                        'role' => $user->role,
                        'is_active' => $user->is_active,
                        'email_verified_at' => $user->email_verified_at,
                        'phone_verified_at' => $user->phone_verified_at,
                        'last_login_at' => $user->last_login_at,
                        'fcm_token' => $user->fcm_token,
                        'notification_preferences' => $user->notification_preferences,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]);
                }
            }
        }

        // حذف جدول users_new
        Schema::dropIfExists('users_new');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة إنشاء جدول users_new
        Schema::create('users_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'parent'])->default('parent');
            $table->boolean('is_active')->default(true);
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('fcm_token')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('phone');
            $table->index('email');
            $table->index('role');
            $table->index('is_active');
        });

        // إزالة الحقول المضافة من جدول users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropIndex(['phone']);
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropIndex(['is_active']);
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('users', 'fcm_token')) {
                $table->dropColumn('fcm_token');
            }
            if (Schema::hasColumn('users', 'notification_preferences')) {
                $table->dropColumn('notification_preferences');
            }
        });
    }
};

