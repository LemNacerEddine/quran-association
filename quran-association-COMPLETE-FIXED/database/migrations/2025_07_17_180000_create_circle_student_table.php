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
        Schema::create('circle_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('circles')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('enrollment_date')->default(now());
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // منع التسجيل المكرر للطالب في نفس الحلقة
            $table->unique(['circle_id', 'student_id']);
            
            // فهارس للبحث السريع
            $table->index(['circle_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_student');
    }
};

