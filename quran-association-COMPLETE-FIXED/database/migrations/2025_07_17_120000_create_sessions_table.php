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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('class_schedules')->onDelete('cascade');
            $table->foreignId('circle_id')->constrained('circles')->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->integer('max_students')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('requires_attendance')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // إضافة فهارس للأداء
            $table->index('session_date', 'idx_session_date');
            $table->index('status', 'idx_session_status');
            $table->index(['schedule_id', 'session_date'], 'idx_schedule_session_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

