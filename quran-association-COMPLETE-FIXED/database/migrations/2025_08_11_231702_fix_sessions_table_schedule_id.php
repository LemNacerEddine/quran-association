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
        Schema::table('sessions', function (Blueprint $table) {
            // جعل schedule_id nullable أو إضافة قيمة افتراضية
            if (Schema::hasColumn('sessions', 'schedule_id')) {
                $table->unsignedBigInteger('schedule_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
