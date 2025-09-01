<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('rating')->unsigned()->comment('التقييم من 1 إلى 5');
            $table->text('comment')->nullable()->comment('تعليق على التقييم');
            $table->timestamps();
            
            $table->index(['teacher_id', 'rating']);
            $table->unique(['teacher_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_ratings');
    }
};

