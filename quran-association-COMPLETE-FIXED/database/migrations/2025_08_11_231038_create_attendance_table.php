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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            // present: حاضر، absent: غائب، late: متأخر، excused: غياب بعذر
            $table->integer('points')->default(0); // النقاط المكتسبة
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamp('marked_at')->nullable(); // وقت تسجيل الحضور
            $table->timestamps();
            
            // فهرس مركب لمنع التكرار
            $table->unique(['session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
