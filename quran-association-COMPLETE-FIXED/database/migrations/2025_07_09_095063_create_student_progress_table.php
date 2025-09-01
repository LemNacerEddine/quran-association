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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('surah_name'); // اسم السورة
            $table->integer('from_verse'); // من الآية
            $table->integer('to_verse'); // إلى الآية
            $table->enum('status', ['memorizing', 'reviewing', 'completed']); // حالة الحفظ
            $table->integer('grade')->nullable(); // الدرجة من 10
            $table->text('notes')->nullable(); // ملاحظات المعلم
            $table->date('test_date')->nullable(); // تاريخ الاختبار
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
