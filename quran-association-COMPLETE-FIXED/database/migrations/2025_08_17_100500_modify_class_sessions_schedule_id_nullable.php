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
        Schema::table('class_sessions', function (Blueprint $table) {
            // إزالة القيد الخارجي الحالي
            $table->dropForeign(['schedule_id']);
            
            // تعديل العمود ليصبح nullable
            $table->foreignId('schedule_id')->nullable()->change();
            
            // إعادة إضافة القيد الخارجي مع nullable
            $table->foreign('schedule_id')->references('id')->on('class_schedules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            // إزالة القيد الخارجي
            $table->dropForeign(['schedule_id']);
            
            // إعادة العمود إلى حالته الأصلية (غير nullable)
            $table->foreignId('schedule_id')->change();
            
            // إعادة إضافة القيد الخارجي الأصلي
            $table->foreign('schedule_id')->references('id')->on('class_schedules')->onDelete('cascade');
        });
    }
};

