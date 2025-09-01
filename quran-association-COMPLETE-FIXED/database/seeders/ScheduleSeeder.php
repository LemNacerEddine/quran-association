<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\AbsenceReason;
use App\Models\Circle;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء أسباب الغياب الافتراضية
        AbsenceReason::createDefaultReasons();

        // الحصول على الحلقات الموجودة
        $circles = Circle::all();

        if ($circles->isEmpty()) {
            $this->command->warn('لا توجد حلقات في قاعدة البيانات. يرجى إنشاء الحلقات أولاً.');
            return;
        }

        // إنشاء جدولة للحلقات
        $schedules = [
            [
                'circle_id' => $circles->first()->id,
                'schedule_name' => 'حلقة الفجر - الأحد',
                'description' => 'حلقة تحفيظ القرآن الكريم صباح الأحد',
                'day_of_week' => 'sunday',
                'start_time' => '07:00',
                'end_time' => '08:30',
                'session_type' => 'morning',
                'location' => 'القاعة الرئيسية',
                'is_active' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth()->addMonths(2),
                'notes' => 'حلقة مخصصة لحفظ القرآن الكريم للمبتدئين'
            ],
            [
                'circle_id' => $circles->first()->id,
                'schedule_name' => 'حلقة المساء - الثلاثاء',
                'description' => 'حلقة تحفيظ القرآن الكريم مساء الثلاثاء',
                'day_of_week' => 'tuesday',
                'start_time' => '17:00',
                'end_time' => '18:30',
                'session_type' => 'evening',
                'location' => 'القاعة الرئيسية',
                'is_active' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth()->addMonths(2),
                'notes' => 'حلقة مسائية للطلاب الذين لا يستطيعون الحضور صباحاً'
            ]
        ];

        if ($circles->count() > 1) {
            $schedules[] = [
                'circle_id' => $circles->skip(1)->first()->id,
                'schedule_name' => 'حلقة الصباح - الخميس',
                'description' => 'حلقة تحفيظ القرآن الكريم صباح الخميس',
                'day_of_week' => 'thursday',
                'start_time' => '08:00',
                'end_time' => '09:30',
                'session_type' => 'morning',
                'location' => 'القاعة الثانوية',
                'is_active' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth()->addMonths(2),
                'notes' => 'حلقة للطلاب المتقدمين في الحفظ'
            ];
        }

        if ($circles->count() > 2) {
            $schedules[] = [
                'circle_id' => $circles->skip(2)->first()->id,
                'schedule_name' => 'حلقة السبت الصباحية',
                'description' => 'حلقة تحفيظ القرآن الكريم صباح السبت',
                'day_of_week' => 'saturday',
                'start_time' => '09:00',
                'end_time' => '10:30',
                'session_type' => 'morning',
                'location' => 'القاعة الثالثة',
                'is_active' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth()->addMonths(2),
                'notes' => 'حلقة نهاية الأسبوع للطلاب والطالبات'
            ];
        }

        foreach ($schedules as $scheduleData) {
            $schedule = ClassSchedule::create($scheduleData);

            // إنشاء جلسات للأسابيع الماضية والقادمة
            $this->createSessionsForSchedule($schedule);
        }

        $this->command->info('تم إنشاء الجدولة والجلسات التجريبية بنجاح.');
    }

    private function createSessionsForSchedule(ClassSchedule $schedule)
    {
        $startDate = now()->subWeeks(2); // أسبوعين ماضيين
        $endDate = now()->addWeeks(4); // 4 أسابيع قادمة

        $current = $startDate->copy();
        $sessionsCreated = 0;

        while ($current <= $endDate && $sessionsCreated < 10) {
            // التحقق من اليوم المطلوب
            if (strtolower($current->format('l')) === $schedule->day_of_week) {
                $status = 'scheduled';
                $attendanceTaken = false;

                // تحديد حالة الجلسة حسب التاريخ
                if ($current->isPast()) {
                    $status = 'completed';
                    $attendanceTaken = true;
                } elseif ($current->isToday()) {
                    $status = 'ongoing';
                }

                $session = ClassSession::create([
                    'schedule_id' => $schedule->id,
                    'circle_id' => $schedule->circle_id,
                    'teacher_id' => $schedule->circle->teacher_id,
                    'session_title' => $schedule->schedule_name . ' - ' . $current->format('Y-m-d'),
                    'session_description' => 'جلسة تحفيظ القرآن الكريم',
                    'session_date' => $current->toDateString(),
                    'actual_start_time' => $status !== 'scheduled' ? $schedule->start_time : null,
                    'actual_end_time' => $status === 'completed' ? $schedule->end_time : null,
                    'status' => $status,
                    'lesson_content' => $this->getRandomLessonContent(),
                    'homework' => $this->getRandomHomework(),
                    'session_notes' => 'جلسة مثمرة ومفيدة للطلاب',
                    'attendance_taken' => $attendanceTaken,
                    'attendance_taken_at' => $attendanceTaken ? $current->copy()->addHour() : null,
                    'attendance_taken_by' => 1 // افتراض أن المستخدم الأول هو المدير
                ]);

                // إنشاء بيانات حضور تجريبية للجلسات المكتملة
                if ($status === 'completed') {
                    $this->createAttendanceForSession($session);
                }

                $sessionsCreated++;
            }
            $current->addDay();
        }
    }

    private function createAttendanceForSession(ClassSession $session)
    {
        $students = $session->circle->students;
        $totalStudents = $students->count();
        
        if ($totalStudents === 0) {
            return;
        }

        $presentCount = 0;
        $absentCount = 0;

        foreach ($students as $student) {
            // 85% احتمال الحضور
            $isPresent = rand(1, 100) <= 85;
            
            $status = $isPresent ? 'present' : 'absent';
            $arrivalTime = null;
            $absenceReasonId = null;
            $absenceReason = null;

            if ($isPresent) {
                $presentCount++;
                // وقت وصول عشوائي (قبل أو بعد الوقت المحدد بـ 10 دقائق)
                $scheduledTime = Carbon::parse($session->schedule->start_time);
                $arrivalTime = $scheduledTime->copy()->addMinutes(rand(-10, 15))->format('H:i');
                
                // تحديد إذا كان متأخراً
                if ($arrivalTime > $session->schedule->start_time) {
                    $status = 'late';
                }
            } else {
                $absentCount++;
                // اختيار سبب غياب عشوائي
                $absenceReasons = AbsenceReason::active()->get();
                if ($absenceReasons->isNotEmpty()) {
                    $reason = $absenceReasons->random();
                    $absenceReasonId = $reason->id;
                    $absenceReason = $reason->reason_name;
                    if ($reason->is_excused) {
                        $status = 'excused';
                    }
                }
            }

            \App\Models\AttendanceSession::create([
                'session_id' => $session->id,
                'student_id' => $student->id,
                'circle_id' => $session->circle_id,
                'status' => $status,
                'arrival_time' => $arrivalTime,
                'absence_reason' => $absenceReason,
                'notes' => $isPresent ? 'حضور منتظم' : 'غياب',
                'recorded_by' => 1,
                'recorded_at' => Carbon::parse($session->session_date)->setTimeFromTimeString($session->actual_end_time ?? '12:00'),
                'participation_score' => $isPresent ? rand(7, 10) : null,
                'behavior_notes' => $isPresent ? 'سلوك ممتاز' : null
            ]);
        }

        // تحديث إحصائيات الجلسة
        $session->update([
            'total_students' => $totalStudents,
            'present_students' => $presentCount,
            'absent_students' => $absentCount,
            'attendance_percentage' => $totalStudents > 0 ? ($presentCount / $totalStudents) * 100 : 0
        ]);
    }

    private function getRandomLessonContent(): string
    {
        $lessons = [
            'حفظ سورة الفاتحة مع التجويد',
            'مراجعة سورة البقرة من الآية 1 إلى 20',
            'حفظ سورة آل عمران من الآية 1 إلى 10',
            'تسميع سورة النساء مع التصحيح',
            'حفظ سورة المائدة الآيات 1-15',
            'مراجعة الحفظ السابق وتثبيته',
            'حفظ سورة الأنعام مع فهم المعاني',
            'تسميع وتصحيح الأخطاء',
            'حفظ سورة الأعراف الجزء الأول',
            'مراجعة شاملة للسور المحفوظة'
        ];

        return $lessons[array_rand($lessons)];
    }

    private function getRandomHomework(): string
    {
        $homework = [
            'مراجعة السورة المحفوظة 3 مرات يومياً',
            'حفظ 5 آيات جديدة للجلسة القادمة',
            'تسميع السورة لولي الأمر',
            'كتابة السورة المحفوظة من الذاكرة',
            'الاستماع للتلاوة الصحيحة 10 مرات',
            'حفظ معاني الكلمات الصعبة',
            'تطبيق أحكام التجويد المتعلمة',
            'مراجعة الحفظ مع زميل',
            'تسجيل التلاوة والاستماع إليها',
            'حفظ آيات جديدة حسب القدرة'
        ];

        return $homework[array_rand($homework)];
    }
}

