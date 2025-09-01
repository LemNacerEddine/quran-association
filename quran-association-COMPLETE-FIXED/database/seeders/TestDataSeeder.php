<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\Student;
use App\Models\Session;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // فحص وجود البيانات أولاً
        if (Teacher::count() > 0) {
            $this->command->info('البيانات موجودة بالفعل!');
            return;
        }

        // إنشاء المعلمين
        $teachers = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed@quran.com', 'phone' => '0501234567'],
            ['name' => 'فاطمة علي', 'email' => 'fatima@quran.com', 'phone' => '0501234568'],
            ['name' => 'محمد عبدالله', 'email' => 'mohammed@quran.com', 'phone' => '0501234569'],
            ['name' => 'عائشة حسن', 'email' => 'aisha@quran.com', 'phone' => '0501234570'],
            ['name' => 'يوسف إبراهيم', 'email' => 'youssef@quran.com', 'phone' => '0501234571'],
        ];

        $teacherModels = [];
        foreach ($teachers as $teacher) {
            $teacherModels[] = Teacher::create($teacher);
        }

        // إنشاء الحلقات
        $circles = [
            ['name' => 'حلقة الفجر', 'teacher_id' => $teacherModels[0]->id, 'description' => 'حلقة تحفيظ القرآن الكريم - الفجر'],
            ['name' => 'حلقة المغرب', 'teacher_id' => $teacherModels[1]->id, 'description' => 'حلقة تحفيظ القرآن الكريم - المغرب'],
            ['name' => 'حلقة العصر', 'teacher_id' => $teacherModels[2]->id, 'description' => 'حلقة تحفيظ القرآن الكريم - العصر'],
            ['name' => 'حلقة الضحى', 'teacher_id' => $teacherModels[3]->id, 'description' => 'حلقة تحفيظ القرآن الكريم - الضحى'],
            ['name' => 'حلقة العشاء', 'teacher_id' => $teacherModels[4]->id, 'description' => 'حلقة تحفيظ القرآن الكريم - العشاء'],
        ];

        $circleModels = [];
        foreach ($circles as $circle) {
            $circleModels[] = Circle::create($circle);
        }

        // إنشاء الطلاب
        $students = [
            ['name' => 'عبدالرحمن أحمد', 'phone' => '0501111111', 'circle_id' => $circleModels[0]->id, 'parent_phone' => '0501234567', 'age' => 12, 'gender' => 'male'],
            ['name' => 'محمد علي', 'phone' => '0501111112', 'circle_id' => $circleModels[0]->id, 'parent_phone' => '0501234567', 'age' => 13, 'gender' => 'male'],
            ['name' => 'فاطمة محمد', 'phone' => '0501111113', 'circle_id' => $circleModels[1]->id, 'parent_phone' => '0501234568', 'age' => 11, 'gender' => 'female'],
            ['name' => 'عائشة عبدالله', 'phone' => '0501111114', 'circle_id' => $circleModels[1]->id, 'parent_phone' => '0501234568', 'age' => 12, 'gender' => 'female'],
            ['name' => 'يوسف حسن', 'phone' => '0501111115', 'circle_id' => $circleModels[2]->id, 'parent_phone' => '0501234569', 'age' => 14, 'gender' => 'male'],
            ['name' => 'زينب إبراهيم', 'phone' => '0501111116', 'circle_id' => $circleModels[2]->id, 'parent_phone' => '0501234569', 'age' => 13, 'gender' => 'female'],
            ['name' => 'عمر سالم', 'phone' => '0501111117', 'circle_id' => $circleModels[3]->id, 'parent_phone' => '0501234570', 'age' => 15, 'gender' => 'male'],
            ['name' => 'خديجة أحمد', 'phone' => '0501111118', 'circle_id' => $circleModels[3]->id, 'parent_phone' => '0501234570', 'age' => 12, 'gender' => 'female'],
            ['name' => 'حسن محمود', 'phone' => '0501111119', 'circle_id' => $circleModels[4]->id, 'parent_phone' => '0501234571', 'age' => 16, 'gender' => 'male'],
            ['name' => 'مريم عبدالعزيز', 'phone' => '0501111120', 'circle_id' => $circleModels[4]->id, 'parent_phone' => '0501234571', 'age' => 14, 'gender' => 'female'],
            ['name' => 'إبراهيم سعد', 'phone' => '0501111121', 'circle_id' => $circleModels[0]->id, 'parent_phone' => '0501234572', 'age' => 11, 'gender' => 'male'],
            ['name' => 'نور الهدى', 'phone' => '0501111122', 'circle_id' => $circleModels[1]->id, 'parent_phone' => '0501234572', 'age' => 13, 'gender' => 'female'],
            ['name' => 'عبدالله راشد', 'phone' => '0501111123', 'circle_id' => $circleModels[2]->id, 'parent_phone' => '0501234573', 'age' => 15, 'gender' => 'male'],
            ['name' => 'سارة محمد', 'phone' => '0501111124', 'circle_id' => $circleModels[3]->id, 'parent_phone' => '0501234573', 'age' => 12, 'gender' => 'female'],
            ['name' => 'خالد عبدالرحمن', 'phone' => '0501111125', 'circle_id' => $circleModels[4]->id, 'parent_phone' => '0501234574', 'age' => 14, 'gender' => 'male'],
        ];

        $studentModels = [];
        foreach ($students as $student) {
            $studentModels[] = Student::create($student);
        }

        // إنشاء الجلسات للشهر الماضي
        $sessions = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // تخطي أيام الجمعة والسبت
            if ($date->dayOfWeek == Carbon::FRIDAY || $date->dayOfWeek == Carbon::SATURDAY) {
                continue;
            }

            foreach ($circleModels as $circle) {
                $sessions[] = Session::create([
                    'schedule_id' => 1, // افتراضي
                    'circle_id' => $circle->id,
                    'teacher_id' => $circle->teacher_id,
                    'session_title' => 'جلسة تحفيظ القرآن الكريم',
                    'session_description' => 'جلسة تحفيظ وتلاوة القرآن الكريم',
                    'session_date' => $date->format('Y-m-d'),
                    'actual_start_time' => '16:00:00',
                    'actual_end_time' => '17:00:00',
                    'status' => 'completed',
                    'lesson_content' => 'تحفيظ وتلاوة القرآن الكريم',
                    'session_notes' => 'جلسة مكتملة',
                    'attendance_taken' => true,
                    'attendance_taken_at' => $date->format('Y-m-d H:i:s'),
                ]);
            }
        }

        // إنشاء سجلات الحضور
        foreach ($sessions as $session) {
            $circleStudents = collect($studentModels)->where('circle_id', $session->circle_id);
            
            foreach ($circleStudents as $student) {
                // احتمالية الحضور 85%
                $isPresent = rand(1, 100) <= 85;
                $isLate = $isPresent && rand(1, 100) <= 15; // 15% احتمال التأخير إذا كان حاضراً
                
                $status = 'absent';
                if ($isPresent) {
                    $status = $isLate ? 'late' : 'present';
                }

                $points = 0;
                $memorizationPoints = 0;
                $finalPoints = 0;

                if ($status == 'present') {
                    $points = rand(8, 10); // نقاط الحضور
                    $memorizationPoints = rand(5, 10); // نقاط الحفظ
                    $finalPoints = $points + $memorizationPoints;
                } elseif ($status == 'late') {
                    $points = rand(5, 7); // نقاط أقل للمتأخرين
                    $memorizationPoints = rand(3, 8);
                    $finalPoints = $points + $memorizationPoints;
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'session_id' => $session->id,
                    'status' => $status,
                    'points' => $points,
                    'memorization_points' => $memorizationPoints,
                    'final_points' => $finalPoints,
                    'notes' => $status == 'present' ? 'حضور ممتاز' : ($status == 'late' ? 'تأخير' : 'غياب'),
                ]);
            }
        }

        // إنشاء أولياء الأمور
        $parents = [
            ['phone' => '0501234567', 'verification_code' => '4567'],
            ['phone' => '0501234568', 'verification_code' => '4568'],
            ['phone' => '0501234569', 'verification_code' => '4569'],
            ['phone' => '0501234570', 'verification_code' => '4570'],
            ['phone' => '0501234571', 'verification_code' => '4571'],
            ['phone' => '0501234572', 'verification_code' => '4572'],
            ['phone' => '0501234573', 'verification_code' => '4573'],
            ['phone' => '0501234574', 'verification_code' => '4574'],
        ];

        foreach ($parents as $parent) {
            User::create([
                'name' => 'ولي أمر',
                'email' => $parent['phone'] . '@parent.com',
                'phone' => $parent['phone'],
                'password' => Hash::make($parent['verification_code']),
                'role' => 'parent',
                'verification_code' => $parent['verification_code'],
            ]);
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('المعلمين: ' . count($teacherModels));
        $this->command->info('الحلقات: ' . count($circleModels));
        $this->command->info('الطلاب: ' . count($studentModels));
        $this->command->info('الجلسات: ' . count($sessions));
        $this->command->info('سجلات الحضور: ' . Attendance::count());
    }
}
