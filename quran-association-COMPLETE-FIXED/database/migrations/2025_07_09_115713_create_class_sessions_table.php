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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('class_schedules')->onDelete('cascade');
            $table->foreignId('circle_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->string('session_title'); // عنوان الجلسة
            $table->text('session_description')->nullable(); // وصف الجلسة
            $table->date('session_date'); // تاريخ الجلسة
            $table->time('actual_start_time')->nullable(); // وقت البداية الفعلي
            $table->time('actual_end_time')->nullable(); // وقت النهاية الفعلي
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->text('lesson_content')->nullable(); // محتوى الدرس
            $table->text('homework')->nullable(); // الواجبات المنزلية
            $table->text('session_notes')->nullable(); // ملاحظات الجلسة
            $table->integer('total_students')->default(0); // إجمالي الطلاب المسجلين
            $table->integer('present_students')->default(0); // عدد الطلاب الحاضرين
            $table->integer('absent_students')->default(0); // عدد الطلاب الغائبين
            $table->decimal('attendance_percentage', 5, 2)->default(0); // نسبة الحضور
            $table->boolean('attendance_taken')->default(false); // هل تم تسجيل الحضور
            $table->timestamp('attendance_taken_at')->nullable(); // وقت تسجيل الحضور
            $table->foreignId('attendance_taken_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancellation_reason')->nullable(); // سبب الإلغاء
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index(['circle_id', 'session_date']);
            $table->index(['teacher_id', 'session_date']);
            $table->index(['session_date', 'status']);
            $table->index('attendance_taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};

