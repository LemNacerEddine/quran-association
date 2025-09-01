<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إنشاء جدول schedule_days لحفظ أيام الجدولة المتعددة
        Schema::create('schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('class_schedules')->onDelete('cascade');
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('session_type', ['morning', 'afternoon', 'evening'])->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // فهرس مركب لمنع التكرار
            $table->unique(['schedule_id', 'day_of_week', 'start_time']);
        });

        // تحديث جدول class_schedules لإزالة الحقول المنقولة
        Schema::table('class_schedules', function (Blueprint $table) {
            // إضافة حقول جديدة
            $table->boolean('has_multiple_days')->default(false)->after('is_active');
            $table->json('default_settings')->nullable()->after('has_multiple_days');
            
            // الاحتفاظ بالحقول القديمة للتوافق مع النظام الحالي
            // سيتم إزالتها لاحقاً بعد نقل البيانات
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_days');
        
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn(['has_multiple_days', 'default_settings']);
        });
    }
};