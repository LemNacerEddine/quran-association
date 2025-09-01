<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Circle;
use App\Models\Session;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\MemorizationPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class OrganizedTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تنظيف البيانات القديمة
        $this->cleanOldData();
        
        // إنشاء المعلم
        $teacher = $this->createTeacher();
        
        // إنشاء الحلقة
        $circle = $this->createCircle($teacher);
        
        // إنشاء الطلاب
        $students = $this->createStudents($circle);
        
        // إنشاء أولياء الأمور
        $parents = $this->createParents($students);
        
        // إنشاء الجلسات
        $sessions = $this->createSessions($circle, $students);
        
        // إنشاء بيانات الحضور والنقاط
        $this->createAttendanceAndPoints($sessions, $students);
        
        $this->command->info('✅ تم إنشاء البيانات التجريبية المنظمة بنجاح!');
        $this->command->info('📊 الإحصائيات:');
        $this->command->info("   - المعلمين: 1");
        $this->command->info("   - الحلقات: 1");
        $this->command->info("   - الطلاب: " . count($students));
        $this->command->info("   - أولياء الأمور: " . count($parents));
        $this->command->info("   - الجلسات: " . count($sessions));
        $this->command->info("   - سجلات الحضور: " . (count($sessions) * count($students)));
    }
    
    /**
     * تنظيف البيانات القديمة
     */
    private function cleanOldData(): void
    {
        $this->command->info('🧹 تنظيف البيانات القديمة...');
        
        // تعطيل فحص المفاتيح الخارجية مؤقتاً
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // حذف البيانات بالترتيب الصحيح لتجنب مشاكل المفاتيح الخارجية
        MemorizationPoint::query()->delete();
        Attendance::query()->delete();
        Session::query()->delete();
        \DB::table('student_circles')->delete();
        \DB::table('guardian_student')->delete(); // الاسم الصحيح للجدول
        Circle::query()->delete();
        Student::query()->delete();
        \DB::table('guardians')->delete(); // استخدام اسم الجدول مباشرة
        Teacher::query()->delete();
        
        // إعادة تفعيل فحص المفاتيح الخارجية
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ تم تنظيف البيانات القديمة');
    }
    
    /**
     * إنشاء المعلم
     */
    private function createTeacher(): Teacher
    {
        $this->command->info('👨‍🏫 إنشاء المعلم...');
        
        return Teacher::create([
            'name' => 'أحمد محمد الأستاذ',
            'phone' => '0501234888',
            'email' => 'ahmed.teacher@quran.com',
            'specialization' => 'تحفيظ القرآن الكريم',
            'experience' => 8,
            'qualification' => 'بكالوريوس الشريعة',
            'gender' => 'male',
            'birth_date' => '1985-01-01',
            'address' => 'الرياض، المملكة العربية السعودية',
            'is_active' => true
        ]);
    }
    
    /**
     * إنشاء الحلقة
     */
    private function createCircle(Teacher $teacher): Circle
    {
        $this->command->info('🏫 إنشاء الحلقة...');
        
        return Circle::create([
            'name' => 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
            'description' => 'حلقة تحفيظ للمستوى المتوسط تركز على حفظ الأجزاء من الأول إلى العاشر مع التركيز على التجويد والفهم',
            'teacher_id' => $teacher->id,
            'max_students' => 10,
            'location' => 'قاعة التحفيظ الرئيسية',
            'schedule_days' => 'الأحد، الثلاثاء، الخميس',
            'start_time' => '16:00:00',
            'end_time' => '18:00:00',
            'is_active' => true
        ]);
    }
    
    /**
     * إنشاء الطلاب
     */
    private function createStudents(Circle $circle): array
    {
        $this->command->info('👥 إنشاء الطلاب...');
        
        $studentsData = [
            [
                'name' => 'عبدالرحمن أحمد',
                'phone' => '0501111111',
                'parent_phone' => '0501234567',
                'birth_date' => '2012-03-15',
                'gender' => 'male',
                'age' => 12,
                'notes' => 'طالب متميز في الحفظ والتلاوة'
            ],
            [
                'name' => 'فاطمة محمد',
                'phone' => '0501111112',
                'parent_phone' => '0501234568',
                'birth_date' => '2013-07-22',
                'gender' => 'female',
                'age' => 11,
                'notes' => 'طالبة مجتهدة ومنتظمة في الحضور'
            ],
            [
                'name' => 'محمد علي',
                'phone' => '0501111113',
                'parent_phone' => '0501234569',
                'birth_date' => '2011-11-08',
                'gender' => 'male',
                'age' => 13,
                'notes' => 'طالب نشط ومتفاعل في الحلقة'
            ],
            [
                'name' => 'عائشة سالم',
                'phone' => '0501111114',
                'parent_phone' => '0501234570',
                'birth_date' => '2014-01-30',
                'gender' => 'female',
                'age' => 10,
                'notes' => 'طالبة بحاجة لمزيد من التشجيع'
            ],
            [
                'name' => 'يوسف إبراهيم',
                'phone' => '0501111115',
                'parent_phone' => '0501234571',
                'birth_date' => '2010-09-12',
                'gender' => 'male',
                'age' => 14,
                'notes' => 'طالب ذكي لكن يحتاج لمزيد من الانتظام'
            ]
        ];
        
        $students = [];
        foreach ($studentsData as $studentData) {
            $student = Student::create([
                'name' => $studentData['name'],
                'phone' => $studentData['phone'],
                'parent_phone' => $studentData['parent_phone'],
                'birth_date' => $studentData['birth_date'],
                'gender' => $studentData['gender'],
                'age' => $studentData['age'],
                'education_level' => 'ابتدائي',
                'address' => 'الرياض، المملكة العربية السعودية',
                'is_active' => true,
                'notes' => $studentData['notes']
            ]);
            
            // ربط الطالب بالحلقة
            $circle->students()->attach($student->id, [
                'enrolled_at' => '2024-05-01',
                'is_active' => true,
                'notes' => 'انضم في بداية الفصل الدراسي'
            ]);
            
            $students[] = $student;
        }
        
        return $students;
    }
    
    /**
     * إنشاء أولياء الأمور
     */
    private function createParents(array $students): array
    {
        $this->command->info('👨‍👩‍👧‍👦 إنشاء أولياء الأمور...');
        
        $parentsData = [
            [
                'name' => 'أحمد عبدالله',
                'phone' => '0501234567',
                'email' => 'ahmed.parent@gmail.com',
                'login_code' => '4567',
                'student_index' => 0
            ],
            [
                'name' => 'محمد حسن',
                'phone' => '0501234568',
                'email' => 'mohammed.parent@gmail.com',
                'login_code' => '4568',
                'student_index' => 1
            ],
            [
                'name' => 'علي أحمد',
                'phone' => '0501234569',
                'email' => 'ali.parent@gmail.com',
                'login_code' => '4569',
                'student_index' => 2
            ],
            [
                'name' => 'سالم محمد',
                'phone' => '0501234570',
                'email' => 'salem.parent@gmail.com',
                'login_code' => '4570',
                'student_index' => 3
            ],
            [
                'name' => 'إبراهيم يوسف',
                'phone' => '0501234571',
                'email' => 'ibrahim.parent@gmail.com',
                'login_code' => '4571',
                'student_index' => 4
            ]
        ];
        
        $parents = [];
        foreach ($parentsData as $parentData) {
            $parent = Guardian::create([
                'name' => $parentData['name'],
                'phone' => $parentData['phone'],
                'email' => $parentData['email'],
                'access_code' => $parentData['login_code'],
                'relationship' => 'father',
                'is_active' => true
            ]);
            
            // ربط ولي الأمر بالطالب
            $student = $students[$parentData['student_index']];
            $parent->students()->attach($student->id, [
                'is_primary' => true
            ]);
            
            $parents[] = $parent;
        }
        
        return $parents;
    }
    
    /**
     * إنشاء الجلسات
     */
    private function createSessions(Circle $circle, array $students): array
    {
        $this->command->info('📅 إنشاء الجلسات...');
        
        $sessions = [];
        $startDate = Carbon::parse('2024-05-01');
        $endDate = Carbon::parse('2024-07-31');
        
        // أيام الحلقة: الأحد (0)، الثلاثاء (2)، الخميس (4)
        $sessionDays = [0, 2, 4];
        
        $sessionNumber = 1;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $sessionDays)) {
                $sessionDate = $current->format('Y-m-d');
                
                $session = ClassSession::create([
                    'schedule_id' => null, // جعل schedule_id فارغ مؤقتاً
                    'circle_id' => $circle->id,
                    'teacher_id' => $circle->teacher_id,
                    'session_title' => "الجلسة رقم $sessionNumber",
                    'session_description' => "جلسة تحفيظ ومراجعة - $sessionDate",
                    'session_date' => $sessionDate,
                    'actual_start_time' => '16:00:00',
                    'actual_end_time' => '18:00:00',
                    'status' => 'completed',
                    'lesson_content' => 'حفظ ومراجعة أجزاء من القرآن الكريم',
                    'session_notes' => 'جلسة مراجعة وحفظ جديد',
                    'total_students' => count($students),
                    'present_students' => 0, // سيتم تحديثه لاحقاً
                    'absent_students' => 0, // سيتم تحديثه لاحقاً
                    'attendance_percentage' => 0, // سيتم تحديثه لاحقاً
                    'attendance_taken' => true,
                    'attendance_taken_at' => now(),
                    'attendance_taken_by' => 1 // المدير
                ]);
                
                $sessions[] = $session;
                $sessionNumber++;
            }
            $current->addDay();
        }
        
        return $sessions;
    }
    
    /**
     * إنشاء بيانات الحضور والنقاط
     */
    private function createAttendanceAndPoints(array $sessions, array $students): void
    {
        $this->command->info('📊 إنشاء بيانات الحضور والنقاط...');
        
        // أنماط الحضور لكل طالب (نسبة الحضور المتوقعة)
        $attendancePatterns = [
            0 => 0.95, // عبدالرحمن - 95%
            1 => 0.92, // فاطمة - 92%
            2 => 0.89, // محمد - 89%
            3 => 0.83, // عائشة - 83%
            4 => 0.78  // يوسف - 78%
        ];
        
        // متوسط النقاط لكل طالب
        $averagePoints = [
            0 => 9.2, // عبدالرحمن
            1 => 8.8, // فاطمة
            2 => 8.5, // محمد
            3 => 7.8, // عائشة
            4 => 7.2  // يوسف
        ];
        
        foreach ($sessions as $sessionIndex => $session) {
            foreach ($students as $studentIndex => $student) {
                // تحديد الحضور بناءً على النمط
                $attendanceRate = $attendancePatterns[$studentIndex];
                $isPresent = (rand(1, 100) / 100) <= $attendanceRate;
                
                $attendanceStatus = $isPresent ? 'present' : 'absent';
                $points = $isPresent ? rand(5, 10) : 0;
                $notes = $isPresent ? null : $this->getAbsenceReason();
                
                // إنشاء سجل الحضور
                Attendance::create([
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                    'status' => $attendanceStatus,
                    'points' => $points,
                    'notes' => $notes,
                    'marked_at' => Carbon::parse($session->session_date)->setTime(16, 0, 0),
                ]);
                
                // إنشاء النقاط فقط للطلاب الحاضرين
                if ($isPresent) {
                    $basePoints = $averagePoints[$studentIndex];
                    $variation = rand(-15, 15) / 10; // تنويع ±1.5 نقطة
                    $totalPoints = max(0, min(10, $basePoints + $variation));
                    
                    MemorizationPoint::create([
                        'student_id' => $student->id,
                        'date' => $session->session_date,
                        'session_type' => 'evening',
                        'points' => round($totalPoints, 1),
                        'memorized_content' => 'حفظ وتلاوة مع مراجعة الأجزاء السابقة',
                        'teacher_notes' => 'أداء ممتاز، استمر على هذا المستوى',
                        'recorded_by' => 1,
                        'recorded_at' => now(),
                    ]);
                }
            }
        }
    }
    
    /**
     * توزيع النقاط حسب النسبة
     */
    private function distributePoints(float $totalPoints, float $percentage): float
    {
        $points = $totalPoints * $percentage;
        $variation = rand(-10, 10) / 100; // تنويع ±10%
        return max(0, round($points + ($points * $variation), 1));
    }
    
    /**
     * الحصول على ملاحظات الجلسة
     */
    private function getSessionNotes(int $sessionNumber): string
    {
        $notes = [
            "جلسة مراجعة وحفظ جديد",
            "التركيز على التجويد والتلاوة",
            "مراجعة الأجزاء المحفوظة سابقاً",
            "حفظ آيات جديدة مع التفسير",
            "تطبيق أحكام التجويد العملية",
            "مسابقة في الحفظ والتلاوة",
            "شرح معاني الآيات المحفوظة",
            "تصحيح الأخطاء الشائعة في التلاوة"
        ];
        
        return $notes[($sessionNumber - 1) % count($notes)];
    }
    
    /**
     * الحصول على سبب الغياب
     */
    private function getAbsenceReason(): string
    {
        $reasons = [
            "مرض",
            "سفر مع الأسرة",
            "ظروف عائلية",
            "امتحانات المدرسة",
            "موعد طبي",
            "غياب بدون عذر"
        ];
        
        return $reasons[array_rand($reasons)];
    }
    
    /**
     * الحصول على ملاحظات النقاط
     */
    private function getPointsNotes(float $totalPoints): string
    {
        if ($totalPoints >= 9) {
            return "أداء ممتاز، استمر على هذا المستوى";
        } elseif ($totalPoints >= 8) {
            return "أداء جيد جداً، يمكن التحسن أكثر";
        } elseif ($totalPoints >= 7) {
            return "أداء جيد، يحتاج لمزيد من التركيز";
        } elseif ($totalPoints >= 6) {
            return "أداء مقبول، يحتاج لمزيد من الجهد";
        } else {
            return "أداء ضعيف، يحتاج لمتابعة خاصة";
        }
    }
    
    /**
     * الحصول على اسم سورة عشوائي
     */
    private function getRandomSurah(): string
    {
        $surahs = [
            'الفاتحة', 'البقرة', 'آل عمران', 'النساء', 'المائدة',
            'الأنعام', 'الأعراف', 'الأنفال', 'التوبة', 'يونس',
            'هود', 'يوسف', 'الرعد', 'إبراهيم', 'الحجر',
            'النحل', 'الإسراء', 'الكهف', 'مريم', 'طه'
        ];
        
        return $surahs[array_rand($surahs)];
    }
}

