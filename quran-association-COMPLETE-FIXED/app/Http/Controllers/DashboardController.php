<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\ClassSchedule;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Guardian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $statisticsService = new \App\Services\StatisticsService();

        // إحصائيات أساسية
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalCircles = Circle::count();
        $totalSchedules = ClassSchedule::count();
        $activeSchedules = ClassSchedule::where('is_active', true)->count();
        
        // إحصائيات الجلسات المحدثة
        $sessionsStats = $statisticsService->getSessionsStatistics();
        $attendanceStats = $statisticsService->getAttendanceStatistics();
        
        // إضافة المتغيرات المفقودة
        $totalSessions = $sessionsStats['total_sessions'] ?? 0;
        $todaySessions = $sessionsStats['today_sessions'] ?? 0;
        $completedSessions = $sessionsStats['completed_sessions'] ?? 0;
        $upcomingSessions = $sessionsStats['scheduled_sessions'] ?? 0;
        
        // إحصائيات أولياء الأمور
        $totalGuardians = Guardian::count();
        $activeGuardians = Guardian::where('is_active', true)->count();
        $fatherGuardians = Guardian::where('relationship', 'father')->count();
        $motherGuardians = Guardian::where('relationship', 'mother')->count();
        $newGuardiansThisMonth = Guardian::whereMonth('created_at', now()->month)->count();

        // إحصائيات شهرية
        $newStudentsThisMonth = Student::whereMonth('created_at', now()->month)->count();
        $newTeachersThisMonth = Teacher::whereMonth('created_at', now()->month)->count();
        $newCirclesThisMonth = Circle::whereMonth('created_at', now()->month)->count();

        // الإحصائيات المتقدمة والحقيقية الجديدة
        $topMemorizationStudents = $statisticsService->getTopMemorizationStudentsThisWeek();
        $topAttendanceStudents = $statisticsService->getTopAttendanceStudents();
        $topPointsStudents = $statisticsService->getTopPointsStudents();
        $frequentAbsentStudents = $statisticsService->getFrequentAbsentStudents();
        $frequentLateStudents = $statisticsService->getFrequentLateStudents();
        $studentsNeedingImprovement = $statisticsService->getStudentsNeedingImprovement();
        $topPerformingCircles = $statisticsService->getTopPerformingCircles();
        $topPerformingTeachers = $statisticsService->getTopPerformingTeachers();
        $mostImprovedStudents = $statisticsService->getMostImprovedStudentsThisWeek();
        $dailyPerformanceStats = $statisticsService->getDailyPerformanceStats();
        $generalStatistics = $statisticsService->getGeneralStatistics();

        // إحصائيات الحضور الحقيقية
        $attendanceRate = $generalStatistics['this_month']['avg_attendance'] ?? 0;
        // إحصائيات عامة محدثة
        $stats = [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_circles' => $totalCircles,
            'total_schedules' => $totalSchedules,
            'active_schedules' => $activeSchedules,
            'total_guardians' => $totalGuardians,
            'active_guardians' => $activeGuardians,
            'father_guardians' => $fatherGuardians,
            'mother_guardians' => $motherGuardians,
        ];

        // دمج إحصائيات الجلسات والحضور
        $stats = array_merge($stats, $sessionsStats, $attendanceStats);

        // الجلسات القادمة
        $upcoming_sessions = Session::with(['circle', 'circle.teacher'])
            ->where('session_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('actual_start_time')
            ->limit(5)
            ->get();

        // الحلقات النشطة مع الأداء
        $active_circles = $topPerformingCircles->take(5);

        // آخر الأنشطة الحقيقية
        $recent_activities = collect([
            // أفضل طلاب الحفظ هذا الأسبوع
            ...$topMemorizationStudents->take(2)->map(function ($student) {
                return [
                    'type' => 'top_memorization',
                    'title' => 'تميز في الحفظ',
                    'description' => "الطالب {$student->name} حقق {$student->avg_memorization_rounded} نقطة حفظ",
                    'time' => now(),
                    'icon' => 'fas fa-star',
                    'color' => 'warning'
                ];
            }),
            // الطلاب المحتاجون لمتابعة
            ...$studentsNeedingImprovement->take(2)->map(function ($student) {
                return [
                    'type' => 'needs_attention',
                    'title' => 'يحتاج متابعة',
                    'description' => "الطالب {$student->name} يحتاج لمتابعة إضافية",
                    'time' => now()->subHours(1),
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'danger'
                ];
            }),
            // آخر الطلاب المسجلين
            ...Student::latest()->limit(1)->get()->map(function ($student) {
                return [
                    'type' => 'student_registered',
                    'title' => 'تسجيل طالب جديد',
                    'description' => "تم تسجيل الطالب {$student->name}",
                    'time' => $student->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => 'success'
                ];
            }),
        ])->sortByDesc('time')->take(5);

        // أفضل الطلاب (بيانات حقيقية محسنة)
        $topStudents = $topPointsStudents->map(function($student) {
            return (object)[
                'name' => $student->name,
                'points' => round($student->total_points ?? 0),
                'circle' => $student->circle_name ?? 'غير محدد',
                'avg_points' => $student->avg_points ?? 0,
                'attendance_percentage' => $student->attendance_percentage ?? 0
            ];
        });

        // أفضل المعلمين (بيانات حقيقية من الإحصائيات)
        $topTeachers = $topPerformingTeachers->map(function($teacher) {
            return (object)[
                'name' => $teacher->name,
                'circles_count' => $teacher->circles_count ?? 0,
                'students_count' => $teacher->active_students ?? 0,
                'avg_points' => $teacher->avg_points ?? 0,
                'attendance_percentage' => $teacher->attendance_percentage ?? 0
            ];
        });

        // آخر سجلات الحضور (بيانات حقيقية)
        $recentAttendance = Attendance::with(['student'])
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->select('attendance.*', 'class_sessions.session_date')
            ->latest('attendance.created_at')
            ->limit(5)
            ->get()
            ->map(function($attendance) {
                $statusText = [
                    'present' => 'حاضر',
                    'late' => 'متأخر', 
                    'absent' => 'غائب',
                    'excused' => 'بعذر'
                ];
                
                return (object)[
                    'student_name' => $attendance->student->name ?? 'غير محدد',
                    'status' => $statusText[$attendance->status] ?? $attendance->status,
                    'date' => $attendance->created_at,
                    'points' => $attendance->final_points ?? 0,
                    'session_date' => $attendance->session_date ?? null
                ];
            });

        // إحصائيات الأداء اليومية
        $todayStats = $dailyPerformanceStats['today'];
        $yesterdayStats = $dailyPerformanceStats['yesterday'];
        $thisWeekStats = $dailyPerformanceStats['this_week'];
        
        // متغيرات محسنة بالبيانات الحقيقية
        $todayAttendance = $todayStats['present'] ?? 0;
        $totalPoints = $generalStatistics['this_month']['total_points'] ?? 0;
        $averagePointsPerStudent = $thisWeekStats['avg_points'] ?? 0;
        $averageAttendanceRate = $thisWeekStats['attendance_rate'] ?? 0;
        $activeStudents = $generalStatistics['this_month']['active_students'] ?? 0;
        $monthlyPoints = $generalStatistics['this_month']['total_points'] ?? 0;
        
        // إضافة recentActivities كمتغير منفصل
        $recentActivities = $recent_activities;
        
        // بيانات الرسوم البيانية الحقيقية
        $circleDistribution = $topPerformingCircles;
        $attendanceData = collect([
            $dailyPerformanceStats['today']['attendance_rate'] ?? 0,
            $dailyPerformanceStats['yesterday']['attendance_rate'] ?? 0,
            $dailyPerformanceStats['this_week']['attendance_rate'] ?? 0,
            $generalStatistics['this_month']['avg_attendance'] ?? 0
        ]);
        
        // بيانات شهرية حقيقية
        $monthlyData = collect([
            'هذا الشهر' => $generalStatistics['this_month']['total_points'] ?? 0,
            'هذا الأسبوع' => $thisWeekStats['total_points'] ?? 0,
            'اليوم' => $todayStats['total_points'] ?? 0
        ]);
        
        // متغيرات الرسوم البيانية
        $weeklyLabels = ['اليوم', 'أمس', 'هذا الأسبوع', 'هذا الشهر'];
        $weeklyAttendanceData = [
            $todayStats['attendance_rate'] ?? 0,
            $yesterdayStats['attendance_rate'] ?? 0,
            $thisWeekStats['attendance_rate'] ?? 0,
            $generalStatistics['this_month']['avg_attendance'] ?? 0
        ];
        $monthlyLabels = ['هذا الشهر', 'هذا الأسبوع', 'اليوم'];
        $monthlyAttendanceData = [
            $generalStatistics['this_month']['total_points'] ?? 0,
            $thisWeekStats['total_points'] ?? 0,
            $todayStats['total_points'] ?? 0
        ];

        return view('dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalCircles', 'totalSchedules', 
            'activeSchedules', 'totalSessions', 'todaySessions',
            'totalGuardians', 'activeGuardians', 'fatherGuardians', 'motherGuardians', 'newGuardiansThisMonth',
            'newStudentsThisMonth', 'newTeachersThisMonth', 'newCirclesThisMonth',
            'attendanceRate', 'completedSessions', 'upcomingSessions',
            'stats', 'upcoming_sessions', 'active_circles', 'recent_activities',
            'topStudents', 'topTeachers', 'recentAttendance', 'recentActivities',
            'todayAttendance', 'totalPoints', 'averagePointsPerStudent', 
            'averageAttendanceRate', 'activeStudents', 'monthlyPoints',
            'circleDistribution', 'attendanceData', 'monthlyData',
            'weeklyLabels', 'weeklyAttendanceData', 'monthlyLabels', 'monthlyAttendanceData',
            // الإحصائيات الحقيقية الجديدة والمحسنة
            'topMemorizationStudents', 'topAttendanceStudents', 'topPointsStudents',
            'frequentAbsentStudents', 'frequentLateStudents', 'studentsNeedingImprovement',
            'topPerformingCircles', 'topPerformingTeachers', 'mostImprovedStudents',
            'dailyPerformanceStats', 'generalStatistics'
        ));
    }
}

