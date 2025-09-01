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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['attendance', 'memorization', 'report', 'reminder', 'announcement']);
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('fcm_message_id')->nullable();
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('type');
            $table->index('sent_at');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
