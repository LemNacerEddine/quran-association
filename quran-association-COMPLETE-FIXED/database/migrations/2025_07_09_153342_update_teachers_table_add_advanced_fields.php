<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('teachers', 'national_id')) {
                $table->string('national_id', 20)->nullable()->unique()->after('email');
            }
            
            if (!Schema::hasColumn('teachers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('national_id');
            }
            
            if (!Schema::hasColumn('teachers', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            }
            
            if (!Schema::hasColumn('teachers', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('teachers', 'qualification')) {
                $table->enum('qualification', ['high_school', 'diploma', 'bachelor', 'master', 'phd'])->nullable()->after('specialization');
            }
            
            if (!Schema::hasColumn('teachers', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('qualification');
            }
            
            if (!Schema::hasColumn('teachers', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('experience_years');
            }
            
            if (!Schema::hasColumn('teachers', 'bio')) {
                $table->text('bio')->nullable()->after('hire_date');
            }
            
            if (!Schema::hasColumn('teachers', 'skills')) {
                $table->text('skills')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('teachers', 'photo')) {
                $table->string('photo')->nullable()->after('skills');
            }
            
            if (!Schema::hasColumn('teachers', 'can_receive_notifications')) {
                $table->boolean('can_receive_notifications')->default(true)->after('is_active');
            }
            
            if (!Schema::hasColumn('teachers', 'max_students')) {
                $table->integer('max_students')->default(20)->after('can_receive_notifications');
            }
            
            if (!Schema::hasColumn('teachers', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('max_students');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $columnsToCheck = [
                'national_id',
                'birth_date', 
                'gender',
                'address',
                'qualification',
                'experience_years',
                'hire_date',
                'bio',
                'skills',
                'photo',
                'can_receive_notifications',
                'max_students',
                'salary'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('teachers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

