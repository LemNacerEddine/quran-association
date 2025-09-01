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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('phone')->nullable();
            $table->string('parent_phone');
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('education_level')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('circle_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
