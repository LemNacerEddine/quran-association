<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث enum لإضافة حالة 'missed'
        DB::statement("ALTER TABLE class_sessions MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled', 'missed') NOT NULL DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع enum إلى الحالة السابقة
        DB::statement("ALTER TABLE class_sessions MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled'");
    }
};

