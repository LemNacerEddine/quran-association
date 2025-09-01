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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['attendance', 'memorization', 'report', 'reminder', 'announcement']);
            $table->string('title_template');
            $table->text('body_template');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
