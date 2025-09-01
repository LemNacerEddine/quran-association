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
        Schema::create('memorization_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('session_type', ['morning', 'evening']);
            $table->integer('points')->unsigned()->check('points >= 0 AND points <= 10');
            $table->text('memorized_content')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['student_id', 'date', 'session_type'], 'unique_points');
            
            // Indexes
            $table->index('date');
            $table->index(['student_id', 'date']);
            $table->index('points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorization_points');
    }
};
