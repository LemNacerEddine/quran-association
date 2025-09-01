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
        Schema::table('circles', function (Blueprint $table) {
            // إضافة الحقول الجديدة
            $table->time('time')->nullable()->after('teacher_id');
            $table->integer('duration')->default(60)->after('time');
            $table->string('days')->nullable()->after('duration');
            
            // تحديث الحقول الموجودة
            $table->integer('max_students')->default(20)->change();
            
            // إزالة الحقول القديمة إذا كانت موجودة
            if (Schema::hasColumn('circles', 'schedule_time')) {
                $table->dropColumn('schedule_time');
            }
            if (Schema::hasColumn('circles', 'age_group')) {
                $table->dropColumn('age_group');
            }
            if (Schema::hasColumn('circles', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circles', function (Blueprint $table) {
            // إعادة الحقول القديمة
            $table->string('schedule_time')->nullable();
            $table->string('age_group')->nullable();
            $table->text('notes')->nullable();
            
            // حذف الحقول الجديدة
            $table->dropColumn(['time', 'duration', 'days']);
        });
    }
};

