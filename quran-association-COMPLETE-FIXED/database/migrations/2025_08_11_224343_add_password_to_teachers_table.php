<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('password', 4)->nullable()->after('phone')->comment('آخر 4 أرقام من الهاتف');
            $table->timestamp('last_login_at')->nullable()->after('password');
        });
        
        // تحديث كلمات المرور للمعلمين الموجودين
        DB::statement("UPDATE teachers SET password = RIGHT(phone, 4) WHERE password IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['password', 'last_login_at']);
        });
    }
};
