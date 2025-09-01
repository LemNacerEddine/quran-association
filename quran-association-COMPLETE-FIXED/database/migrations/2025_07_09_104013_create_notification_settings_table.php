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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('notification_type', ['attendance', 'memorization', 'report', 'reminder', 'announcement']);
            $table->boolean('is_enabled')->default(true);
            $table->enum('delivery_method', ['push', 'sms', 'email'])->default('push');
            $table->time('quiet_hours_start')->default('22:00:00');
            $table->time('quiet_hours_end')->default('06:00:00');
            $table->enum('frequency', ['immediate', 'daily_digest', 'weekly_digest'])->default('immediate');
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['user_id', 'notification_type'], 'unique_user_type');
            
            // Index
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
