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
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_title'); // عنوان التقرير
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'custom', 'session']); // نوع التقرير
            $table->date('report_date'); // تاريخ التقرير
            $table->date('period_start'); // بداية الفترة
            $table->date('period_end'); // نهاية الفترة
            $table->foreignId('circle_id')->nullable()->constrained()->onDelete('cascade'); // الحلقة (اختياري)
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('cascade'); // المعلم (اختياري)
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade'); // الطالب (اختياري)
            $table->json('report_data'); // بيانات التقرير (JSON)
            $table->json('statistics'); // الإحصائيات (JSON)
            $table->integer('total_sessions')->default(0); // إجمالي الجلسات
            $table->integer('total_students')->default(0); // إجمالي الطلاب
            $table->integer('total_present')->default(0); // إجمالي الحضور
            $table->integer('total_absent')->default(0); // إجمالي الغياب
            $table->decimal('attendance_percentage', 5, 2)->default(0); // نسبة الحضور
            $table->text('summary')->nullable(); // ملخص التقرير
            $table->text('recommendations')->nullable(); // التوصيات
            $table->boolean('is_automated')->default(true); // هل التقرير تلقائي
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade'); // من أنشأ التقرير
            $table->timestamp('generated_at')->useCurrent(); // وقت إنشاء التقرير
            $table->boolean('is_sent')->default(false); // هل تم إرسال التقرير
            $table->timestamp('sent_at')->nullable(); // وقت الإرسال
            $table->json('recipients')->nullable(); // المستقبلين (JSON)
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index(['report_type', 'report_date']);
            $table->index(['circle_id', 'period_start', 'period_end']);
            $table->index(['teacher_id', 'period_start', 'period_end']);
            $table->index(['student_id', 'period_start', 'period_end']);
            $table->index('is_automated');
            $table->index('is_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_reports');
    }
};

