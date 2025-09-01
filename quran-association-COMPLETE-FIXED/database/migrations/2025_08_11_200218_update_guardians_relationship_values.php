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
        // إضافة عمود relationship_type جديد
        Schema::table('guardians', function (Blueprint $table) {
            $table->string('relationship_type')->nullable()->after('relationship');
        });

        // تحديث القيم من الإنجليزية إلى العربية
        DB::table('guardians')->where('relationship', 'father')->update(['relationship_type' => 'الأب']);
        DB::table('guardians')->where('relationship', 'mother')->update(['relationship_type' => 'الأم']);
        DB::table('guardians')->where('relationship', 'guardian')->update(['relationship_type' => 'ولي الأمر']);
        DB::table('guardians')->where('relationship', 'other')->update(['relationship_type' => 'أخرى']);

        // جعل العمود مطلوب بعد تحديث القيم
        Schema::table('guardians', function (Blueprint $table) {
            $table->string('relationship_type')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn('relationship_type');
        });
    }
};
