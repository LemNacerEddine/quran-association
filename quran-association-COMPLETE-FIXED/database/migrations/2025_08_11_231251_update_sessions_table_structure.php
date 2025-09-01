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
            // إضافة الأعمدة المفقودة إذا لم تكن موجودة
            if (!Schema::hasColumn('sessions', 'title')) {
                $table->string('title')->after('circle_id');
            }
            if (!Schema::hasColumn('sessions', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('sessions', 'lesson_content')) {
                $table->json('lesson_content')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('sessions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('lesson_content');
            }
            if (!Schema::hasColumn('sessions', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('started_at');
            }
            
            // تحديث عمود status إذا كان موجود
            if (Schema::hasColumn('sessions', 'status')) {
                $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'postponed'])
                      ->default('scheduled')->change();
            } else {
                $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'postponed'])
                      ->default('scheduled')->after('end_time');
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
