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
        Schema::create('absence_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason_name'); // اسم السبب
            $table->text('reason_description')->nullable(); // وصف السبب
            $table->enum('reason_type', ['medical', 'family', 'travel', 'emergency', 'personal', 'other']); // نوع السبب
            $table->boolean('requires_documentation')->default(false); // هل يتطلب توثيق
            $table->boolean('is_excused')->default(true); // هل هو عذر مقبول
            $table->integer('max_consecutive_days')->nullable(); // أقصى عدد أيام متتالية
            $table->boolean('affects_attendance_record')->default(true); // هل يؤثر على سجل الحضور
            $table->boolean('is_active')->default(true); // هل السبب نشط
            $table->integer('usage_count')->default(0); // عدد مرات الاستخدام
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index('reason_type');
            $table->index('is_active');
            $table->index('is_excused');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_reasons');
    }
};

