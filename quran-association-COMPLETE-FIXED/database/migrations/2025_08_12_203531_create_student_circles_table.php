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
        Schema::create('student_circles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('circle_id')->constrained('circles')->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // منع التسجيل المكرر في نفس الحلقة
            $table->unique(['student_id', 'circle_id']);
            
            // فهارس للبحث السريع
            $table->index(['student_id', 'is_active']);
            $table->index(['circle_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_circles');
    }
};
