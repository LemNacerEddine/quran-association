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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم ولي الأمر
            $table->string('phone')->unique(); // رقم الهاتف (فريد)
            $table->string('email')->nullable(); // البريد الإلكتروني (اختياري)
            $table->string('national_id')->nullable(); // رقم الهوية (اختياري)
            $table->string('address')->nullable(); // العنوان (اختياري)
            $table->string('job')->nullable(); // المهنة (اختياري)
            $table->string('access_code'); // كود الدخول (افتراضياً آخر 4 أرقام من الهاتف)
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])->default('father'); // صلة القرابة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
