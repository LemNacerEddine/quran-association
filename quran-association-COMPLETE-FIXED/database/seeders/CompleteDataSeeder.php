<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Circle;
use App\Models\ClassSchedule;
use App\Models\Session;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء المستخدمين
        $admin = User::create([
            'name' => 'مدير النظام',
            'phone' => '0500000000',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // إنشاء المعلمين
        $teachers = [
            Teacher::create([
                'name' => 'أحمد محمد الأحمد',
                'phone' => '0501234567',
                'email' => 'ahmed@example.com',
                'qualification' => 'بكالوريوس الشريعة الإسلامية',
                'gender' => 'male',
                'experience' => '8 سنوات في تحفيظ القرآن الكريم',
                'specialization' => 'تحفيظ القرآن الكريم والتجويد',
                'is_active' => true,
            ]),
            Teacher::create([
                'name' => 'محمد العلي السالم',
                'phone' => '0507654321',
                'email' => 'mohammed@example.com',
                'qualification' => 'ماجستير القراءات',
                'gender' => 'male',
                'experience' => '12 سنة في تعليم القراءات العشر',
                'specialization' => 'القراءات العشر والتجويد المتقدم',
                'is_active' => true,
            ]),
            Teacher::create([
                'name' => 'عبد الرحمن سالم المحمد',
                'phone' => '0509876543',
                'email' => 'abdulrahman@example.com',
                'qualification' => 'بكالوريوس التربية الإسلامية',
                'gender' => 'male',
                'experience' => '5 سنوات في تعليم الأطفال',
                'specialization' => 'تحفيظ القرآن للأطفال',
                'is_active' => true,
            ]),
        ];

        // إنشاء الطلاب
        $students = [];
        $studentNames = [
            'عبد الله أحمد محمد', 'محمد سعد العلي', 'أحمد عبد الرحمن سالم',
            'سعد محمد الأحمد', 'علي عبد الله المحمد', 'يوسف أحمد السالم',
            'عمر محمد العلي', 'حسن عبد الرحمن أحمد', 'خالد سعد محمد',
            'فهد علي عبد الله', 'نواف محمد سالم', 'بندر أحمد العلي',
            'سلطان عبد الله سعد', 'تركي محمد الأحمد', 'فيصل علي سالم'
        ];

        foreach ($studentNames as $index => $name) {
            $age = rand(8, 25);
            $students[] = Student::create([
                'name' => $name,
                'age' => $age,
                'phone' => '050' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'parent_phone' => '055' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'address' => 'الرياض - حي ' . ['النرجس', 'الملقا', 'الياسمين', 'الروضة', 'العليا'][rand(0, 4)],
                'birth_date' => Carbon::now()->subYears($age)->format('Y-m-d'),
                'gender' => ['male', 'female'][rand(0, 1)],
                'education_level' => ['ابتدائي', 'متوسط', 'ثانوي', 'جامعي'][rand(0, 3)],
                'notes' => 'طالب ' . ['مجتهد', 'نشيط', 'متميز', 'منتظم'][rand(0, 3)],
                'is_active' => true,
            ]);
        }

        // إنشاء الحلقات
        $circles = [
            Circle::create([
                'name' => 'حلقة الفجر للمبتدئين',
                'teacher_id' => $teachers[0]->id,
                'description' => 'حلقة تحفيظ للمبتدئين بعد صلاة الفجر',
                'max_students' => 15,
                'schedule_days' => 'أحد,ثلاثاء,خميس',
                'start_time' => '06:00',
                'end_time' => '07:00',
                'location' => 'المسجد الكبير - القاعة الأولى',
                'is_active' => true,
            ]),
            Circle::create([
                'name' => 'حلقة المغرب للمتوسطين',
                'teacher_id' => $teachers[1]->id,
                'description' => 'حلقة تحفيظ للمتوسطين بعد صلاة المغرب',
                'max_students' => 12,
                'schedule_days' => 'سبت,اثنين,أربعاء',
                'start_time' => '19:30',
                'end_time' => '20:30',
                'location' => 'المسجد الكبير - القاعة الثانية',
                'is_active' => true,
            ]),
            Circle::create([
                'name' => 'حلقة العصر للأطفال',
                'teacher_id' => $teachers[2]->id,
                'description' => 'حلقة تحفيظ مخصصة للأطفال الصغار',
                'max_students' => 20,
                'schedule_days' => 'يومي',
                'start_time' => '16:00',
                'end_time' => '17:00',
                'location' => 'المسجد الكبير - القاعة الثالثة',
                'is_active' => true,
            ]),
        ];

        // ربط الطلاب بالحلقات
        // حلقة الفجر - 5 طلاب
        for ($i = 0; $i < 5; $i++) {
            $circles[0]->students()->attach($students[$i]->id);
        }

        // حلقة المغرب - 5 طلاب
        for ($i = 5; $i < 10; $i++) {
            $circles[1]->students()->attach($students[$i]->id);
        }

        // حلقة العصر - 5 طلاب
        for ($i = 10; $i < 15; $i++) {
            $circles[2]->students()->attach($students[$i]->id);
        }

        // إنشاء الجدولات
        $schedules = [];
        foreach ($circles as $index => $circle) {
            $schedules[] = ClassSchedule::create([
                'schedule_name' => 'جدولة ' . $circle->name,
                'circle_id' => $circle->id,
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->endOfMonth(),
                'start_time' => $circle->start_time,
                'end_time' => $circle->end_time,
                'recurrence_type' => 'weekly',
                'location' => $circle->location,
                'max_students' => $circle->max_students,
                'is_active' => true,
                'auto_create_sessions' => true,
                'requires_attendance' => true,
                'status' => 'active',
                'created_by' => $admin->id,
                'description' => 'جدولة شهرية لـ' . $circle->name,
            ]);
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('المستخدمون: ' . User::count());
        $this->command->info('المعلمون: ' . Teacher::count());
        $this->command->info('الطلاب: ' . Student::count());
        $this->command->info('الحلقات: ' . Circle::count());
        $this->command->info('الجدولات: ' . ClassSchedule::count());
    }
}

