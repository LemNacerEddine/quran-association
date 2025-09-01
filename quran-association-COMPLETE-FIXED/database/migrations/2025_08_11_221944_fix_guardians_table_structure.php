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
        Schema::table('guardians', function (Blueprint $table) {
            // Check if relationship_type column exists and drop it if it does
            if (Schema::hasColumn('guardians', 'relationship_type')) {
                $table->dropColumn('relationship_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->enum('relationship_type', ['father', 'mother', 'guardian', 'other'])->default('father');
        });
    }
};
