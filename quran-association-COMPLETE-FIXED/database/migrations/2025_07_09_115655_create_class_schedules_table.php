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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_name');
            $table->foreignId('circle_id')->constrained('circles')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('recurrence_type', ['weekly', 'monthly'])->default('weekly');
            $table->string('location')->nullable();
            $table->integer('max_students')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_attendance')->default(true);
            $table->boolean('auto_create_sessions')->default(true);
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // إضافة فهارس للأداء
            $table->index(['start_date', 'end_date'], 'idx_schedule_dates');
            $table->index('status', 'idx_schedule_status');
            $table->index('is_active', 'idx_schedule_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إيقاف فحص Foreign Keys مؤقتاً لتجنب الأخطاء
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('class_schedules');
        Schema::enableForeignKeyConstraints();
    }
};

