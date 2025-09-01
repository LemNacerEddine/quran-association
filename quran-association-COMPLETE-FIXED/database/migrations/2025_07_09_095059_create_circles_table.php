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
        Schema::create('circles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->integer('max_students')->default(10);
            $table->string('schedule_days')->nullable(); // أيام الأسبوع
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circles');
    }
};
