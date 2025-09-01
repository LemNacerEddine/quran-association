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
        Schema::table('attendance', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign('attendance_session_id_foreign');
            
            // Add the correct foreign key constraint pointing to class_sessions table
            $table->foreign('session_id')->references('id')->on('class_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['session_id']);
        });
    }
};
