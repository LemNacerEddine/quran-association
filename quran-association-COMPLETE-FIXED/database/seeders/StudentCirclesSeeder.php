<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Circle;
use Carbon\Carbon;

class StudentCirclesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $students = Student::all();
        $circles = Circle::all();

        if ($students->isEmpty() || $circles->isEmpty()) {
            $this->command->info('لا توجد طلاب أو حلقات لربطها');
            return;
        }

        // ربط كل طالب بحلقة واحدة على الأقل
        foreach ($students as $student) {
            // اختيار حلقة عشوائية
            $primaryCircle = $circles->random();
            
            // ربط الطالب بالحلقة الأساسية
            $student->circles()->attach($primaryCircle->id, [
                'enrolled_at' => Carbon::now()->subDays(rand(1, 30)),
                'is_active' => true,
                'notes' => 'تسجيل أساسي في الحلقة'
            ]);

            // 30% من الطلاب يسجلون في حلقة ثانية
            if (rand(1, 100) <= 30) {
                $availableCircles = $circles->where('id', '!=', $primaryCircle->id);
                if ($availableCircles->isNotEmpty()) {
                    $secondaryCircle = $availableCircles->random();
                    
                    $student->circles()->attach($secondaryCircle->id, [
                        'enrolled_at' => Carbon::now()->subDays(rand(1, 15)),
                        'is_active' => true,
                        'notes' => 'تسجيل إضافي في حلقة ثانية'
                    ]);
                }
            }

            // 10% من الطلاب يسجلون في حلقة ثالثة
            if (rand(1, 100) <= 10) {
                $enrolledCircleIds = $student->circles()->pluck('circles.id')->toArray();
                $availableCircles = $circles->whereNotIn('id', $enrolledCircleIds);
                
                if ($availableCircles->isNotEmpty()) {
                    $thirdCircle = $availableCircles->random();
                    
                    $student->circles()->attach($thirdCircle->id, [
                        'enrolled_at' => Carbon::now()->subDays(rand(1, 7)),
                        'is_active' => true,
                        'notes' => 'تسجيل إضافي في حلقة ثالثة'
                    ]);
                }
            }
        }

        $this->command->info('تم ربط الطلاب بالحلقات بنجاح');
        
        // إحصائيات
        $totalEnrollments = \DB::table('student_circles')->where('student_circles.is_active', true)->count();
        $studentsWithMultipleCircles = Student::whereHas('circles', function($query) {
            $query->where('student_circles.is_active', true);
        }, '>', 1)->count();
        
        $this->command->info("إجمالي التسجيلات: {$totalEnrollments}");
        $this->command->info("الطلاب المسجلون في حلقات متعددة: {$studentsWithMultipleCircles}");
    }
}
