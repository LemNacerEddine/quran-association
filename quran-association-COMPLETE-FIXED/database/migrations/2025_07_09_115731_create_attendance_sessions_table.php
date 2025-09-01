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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('class_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('circle_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->time('arrival_time')->nullable(); // وقت الوصول
            $table->time('departure_time')->nullable(); // وقت المغادرة
            $table->text('absence_reason')->nullable(); // سبب الغياب
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->boolean('parent_notified')->default(false); // هل تم إشعار ولي الأمر
            $table->timestamp('parent_notified_at')->nullable(); // وقت إشعار ولي الأمر
            $table->enum('notification_method', ['sms', 'email', 'app', 'call'])->nullable(); // طريقة الإشعار
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade'); // من سجل الحضور
            $table->timestamp('recorded_at')->useCurrent(); // وقت تسجيل الحضور
            $table->boolean('is_makeup_session')->default(false); // هل هي جلسة تعويضية
            $table->foreignId('makeup_for_session')->nullable()->constrained('class_sessions')->onDelete('set null');
            $table->decimal('participation_score', 3, 1)->nullable(); // درجة المشاركة (من 10)
            $table->text('behavior_notes')->nullable(); // ملاحظات السلوك
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index(['session_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['circle_id', 'status']);
            $table->index('parent_notified');
            $table->index('recorded_at');
            
            // فهرس مركب لمنع التكرار
            $table->unique(['session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};

