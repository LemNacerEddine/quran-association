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
        Schema::table('students', function (Blueprint $table) {
            // إضافة حقل parent_id إذا لم يكن موجوداً
            if (!Schema::hasColumn('students', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('name')->constrained('users')->onDelete('cascade');
            }
            
            // إضافة حقول إضافية للطلاب
            if (!Schema::hasColumn('students', 'total_attendance_points')) {
                $table->integer('total_attendance_points')->default(0)->after('notes');
            }
            
            if (!Schema::hasColumn('students', 'total_memorization_points')) {
                $table->integer('total_memorization_points')->default(0)->after('total_attendance_points');
            }
            
            if (!Schema::hasColumn('students', 'status')) {
                $table->enum('status', ['active', 'inactive', 'transferred', 'graduated'])->default('active')->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('students', 'total_attendance_points')) {
                $table->dropColumn('total_attendance_points');
            }
            
            if (Schema::hasColumn('students', 'total_memorization_points')) {
                $table->dropColumn('total_memorization_points');
            }
            
            if (Schema::hasColumn('students', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
