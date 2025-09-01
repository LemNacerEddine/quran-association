<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Circle;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * أفضل 5 طلاب في الحفظ هذا الأسبوع
     */
    public function getTopMemorizationStudentsThisWeek()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name')
            ->selectRaw('AVG(attendance.memorization_points) as avg_memorization')
            ->selectRaw('COUNT(attendance.id) as sessions_count')
            ->selectRaw('SUM(attendance.memorization_points) as total_memorization')
            ->selectRaw('ROUND(AVG(attendance.memorization_points), 1) as avg_memorization_rounded')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->whereBetween('class_sessions.session_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('attendance.memorization_points')
            ->where('attendance.memorization_points', '>', 0)
            ->groupBy('students.id', 'students.name', 'students.phone', 'circles.name')
            ->orderByDesc('avg_memorization')
            ->orderByDesc('total_memorization')
            ->limit(5)
            ->get();
    }

    /**
     * أفضل 5 طلاب في الالتزام بالحضور
     */
    public function getTopAttendanceStudents()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count')
            ->selectRaw('SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->selectRaw('SUM(attendance.final_points) as total_points')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id', 'students.name', 'students.phone', 'circles.name')
            ->having('total_sessions', '>=', 3) // على الأقل 3 جلسات
            ->orderByDesc('attendance_percentage')
            ->orderByDesc('avg_points')
            ->limit(5)
            ->get();
    }

    /**
     * الطلاب كثيرو التغيب
     */
    public function getFrequentAbsentStudents()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name', 'teachers.name as teacher_name')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->selectRaw('SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as absence_percentage')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->leftJoin('teachers', 'circles.teacher_id', '=', 'teachers.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id', 'students.name', 'students.phone', 'circles.name', 'teachers.name')
            ->having('total_sessions', '>=', 3)
            ->having('absence_percentage', '>=', 25) // أكثر من 25% غياب
            ->orderByDesc('absence_percentage')
            ->orderByDesc('absent_count')
            ->limit(10)
            ->get();
    }

    /**
     * إحصائيات الجلسات المحدثة
     */
    public function getSessionsStatistics()
    {
        $today = Carbon::today();
        
        return [
            'total_sessions' => ClassSession::count(),
            'completed_sessions' => ClassSession::where('status', 'completed')->count(),
            'missed_sessions' => ClassSession::where('status', 'missed')->count(),
            'scheduled_sessions' => ClassSession::where('status', 'scheduled')->count(),
            'ongoing_sessions' => ClassSession::where('status', 'ongoing')->count(),
            'cancelled_sessions' => ClassSession::where('status', 'cancelled')->count(),
            'today_sessions' => ClassSession::whereDate('session_date', $today)->count(),
            'future_sessions' => ClassSession::whereDate('session_date', '>', $today)->count(),
            'past_sessions' => ClassSession::whereDate('session_date', '<', $today)->count(),
            'attendance_taken_sessions' => ClassSession::where('attendance_taken', true)->count(),
            'pending_attendance_sessions' => ClassSession::where('attendance_taken', false)
                ->whereDate('session_date', '<=', $today)
                ->count(),
        ];
    }

    /**
     * إحصائيات الحضور العامة
     */
    public function getAttendanceStatistics()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'total_attendance_records' => Attendance::count(),
            'present_records' => Attendance::where('status', 'present')->count(),
            'absent_records' => Attendance::where('status', 'absent')->count(),
            'late_records' => Attendance::where('status', 'late')->count(),
            'excused_records' => Attendance::where('status', 'excused')->count(),
            'overall_attendance_rate' => $this->calculateOverallAttendanceRate(),
            'monthly_attendance_rate' => $this->calculateMonthlyAttendanceRate(),
            'weekly_attendance_rate' => $this->calculateWeeklyAttendanceRate(),
        ];
    }

    /**
     * حساب معدل الحضور العام
     */
    private function calculateOverallAttendanceRate()
    {
        $totalRecords = Attendance::count();
        if ($totalRecords == 0) return 0;
        
        $presentRecords = Attendance::where('status', 'present')->count();
        return round(($presentRecords / $totalRecords) * 100, 1);
    }

    /**
     * حساب معدل الحضور الشهري
     */
    private function calculateMonthlyAttendanceRate()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $totalRecords = Attendance::join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$startOfMonth, $endOfMonth])
            ->count();
            
        if ($totalRecords == 0) return 0;
        
        $presentRecords = Attendance::join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$startOfMonth, $endOfMonth])
            ->where('attendance.status', 'present')
            ->count();
            
        return round(($presentRecords / $totalRecords) * 100, 1);
    }

    /**
     * حساب معدل الحضور الأسبوعي
     */
    private function calculateWeeklyAttendanceRate()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $totalRecords = Attendance::join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$startOfWeek, $endOfWeek])
            ->count();
            
        if ($totalRecords == 0) return 0;
        
        $presentRecords = Attendance::join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$startOfWeek, $endOfWeek])
            ->where('attendance.status', 'present')
            ->count();
            
        return round(($presentRecords / $totalRecords) * 100, 1);
    }

    /**
     * الطلاب الذين يتأخرون دائماً
     */
    public function getFrequentLateStudents()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name', 'teachers.name as teacher_name')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count')
            ->selectRaw('SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as late_percentage')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->leftJoin('teachers', 'circles.teacher_id', '=', 'teachers.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id', 'students.name', 'students.phone', 'circles.name', 'teachers.name')
            ->having('total_sessions', '>=', 3)
            ->having('late_percentage', '>=', 15) // أكثر من 15% تأخير
            ->orderByDesc('late_percentage')
            ->orderByDesc('late_count')
            ->limit(10)
            ->get();
    }

    /**
     * أفضل 5 طلاب في النقاط الإجمالية
     */
    public function getTopPointsStudents()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('SUM(attendance.final_points) as total_points')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->selectRaw('SUM(attendance.points) as total_attendance_points')
            ->selectRaw('SUM(attendance.memorization_points) as total_memorization_points')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id', 'students.name', 'students.phone', 'circles.name')
            ->having('total_sessions', '>=', 3)
            ->orderByDesc('total_points')
            ->orderByDesc('avg_points')
            ->limit(5)
            ->get();
    }

    /**
     * الطلاب المحتاجون لتحسين
     */
    public function getStudentsNeedingImprovement()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Student::select('students.id', 'students.name', 'students.phone')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('AVG(attendance.final_points) as avg_points')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->selectRaw('AVG(attendance.memorization_points) as avg_memorization')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id', 'students.name', 'students.phone')
            ->having('total_sessions', '>=', 3)
            ->havingRaw('(avg_points < 5 OR attendance_percentage < 70 OR avg_memorization < 2)')
            ->orderBy('avg_points')
            ->orderBy('attendance_percentage')
            ->limit(10)
            ->get();
    }

    /**
     * إحصائيات عامة للنظام
     */
    public function getGeneralStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            // إحصائيات اليوم
            'today' => [
                'sessions' => ClassSession::whereDate('session_date', $today)->count(),
                'completed_sessions' => ClassSession::whereDate('session_date', $today)
                    ->where('status', 'completed')->count(),
                'attendance_taken' => ClassSession::whereDate('session_date', $today)
                    ->where('attendance_taken', true)->count(),
            ],

            // إحصائيات الأسبوع
            'this_week' => [
                'sessions' => ClassSession::where('session_date', '>=', $thisWeek)->count(),
                'avg_attendance' => $this->getAverageAttendanceForPeriod($thisWeek),
                'total_points' => $this->getTotalPointsForPeriod($thisWeek),
            ],

            // إحصائيات الشهر
            'this_month' => [
                'sessions' => ClassSession::where('session_date', '>=', $thisMonth)->count(),
                'avg_attendance' => $this->getAverageAttendanceForPeriod($thisMonth),
                'total_points' => $this->getTotalPointsForPeriod($thisMonth),
                'active_students' => $this->getActiveStudentsForPeriod($thisMonth),
            ],

            // إحصائيات الأداء
            'performance' => [
                'excellent_students' => $this->getStudentsByPerformance('excellent'),
                'good_students' => $this->getStudentsByPerformance('good'),
                'needs_improvement' => $this->getStudentsByPerformance('needs_improvement'),
            ]
        ];
    }

    /**
     * متوسط الحضور لفترة معينة
     */
    private function getAverageAttendanceForPeriod($startDate)
    {
        $result = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $startDate)
            ->selectRaw('AVG(CASE WHEN attendance.status = "present" THEN 100 ELSE 0 END) as avg_attendance')
            ->first();

        return round($result->avg_attendance ?? 0, 1);
    }

    /**
     * إجمالي النقاط لفترة معينة
     */
    private function getTotalPointsForPeriod($startDate)
    {
        $result = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $startDate)
            ->sum('attendance.final_points');

        return $result ?? 0;
    }

    /**
     * الطلاب النشطون لفترة معينة
     */
    private function getActiveStudentsForPeriod($startDate)
    {
        return DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $startDate)
            ->distinct('attendance.student_id')
            ->count('attendance.student_id');
    }

    /**
     * الطلاب حسب مستوى الأداء
     */
    private function getStudentsByPerformance($level)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $query = Student::select('students.id')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('AVG(attendance.final_points) as avg_points')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->groupBy('students.id')
            ->having('total_sessions', '>=', 3);

        switch ($level) {
            case 'excellent':
                $query->having('avg_points', '>=', 8)
                      ->having('attendance_percentage', '>=', 90);
                break;
            case 'good':
                $query->having('avg_points', '>=', 6)
                      ->having('avg_points', '<', 8)
                      ->having('attendance_percentage', '>=', 75);
                break;
            case 'needs_improvement':
                $query->having('avg_points', '<', 6)
                      ->orHaving('attendance_percentage', '<', 75);
                break;
        }

        return $query->count();
    }

    /**
     * ملخص الطالب (للاستخدام في إدارة الطلاب)
     */
    public function getStudentSummary($studentId)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $summary = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('attendance.student_id', $studentId)
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                AVG(attendance.final_points) as avg_points,
                SUM(attendance.final_points) as total_points,
                AVG(attendance.memorization_points) as avg_memorization,
                MAX(class_sessions.session_date) as last_session_date
            ')
            ->first();

        if (!$summary || $summary->total_sessions == 0) {
            return [
                'total_sessions' => 0,
                'attendance_percentage' => 0,
                'avg_points' => 0,
                'performance_level' => 'no_data',
                'last_session' => null,
                'needs_attention' => false
            ];
        }

        $attendancePercentage = round(($summary->present_count / $summary->total_sessions) * 100, 1);
        
        // تحديد مستوى الأداء
        $performanceLevel = 'needs_improvement';
        if ($summary->avg_points >= 8 && $attendancePercentage >= 90) {
            $performanceLevel = 'excellent';
        } elseif ($summary->avg_points >= 6 && $attendancePercentage >= 75) {
            $performanceLevel = 'good';
        }

        // تحديد إذا كان يحتاج انتباه
        $needsAttention = $attendancePercentage < 75 || $summary->avg_points < 5 || $summary->late_count > 3;

        return [
            'total_sessions' => $summary->total_sessions,
            'present_count' => $summary->present_count,
            'late_count' => $summary->late_count,
            'absent_count' => $summary->absent_count,
            'attendance_percentage' => $attendancePercentage,
            'avg_points' => round($summary->avg_points, 1),
            'total_points' => $summary->total_points,
            'avg_memorization' => round($summary->avg_memorization, 1),
            'performance_level' => $performanceLevel,
            'last_session' => $summary->last_session_date,
            'needs_attention' => $needsAttention
        ];
    }

    /**
     * أفضل 5 حلقات من حيث الأداء
     */
    public function getTopPerformingCircles()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Circle::select('circles.id', 'circles.name', 'circles.description', 'teachers.name as teacher_name')
            ->selectRaw('COUNT(DISTINCT attendance.student_id) as active_students')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->selectRaw('ROUND(AVG(attendance.memorization_points), 1) as avg_memorization')
            ->join('students', 'circles.id', '=', 'students.circle_id')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('teachers', 'circles.teacher_id', '=', 'teachers.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->where('circles.is_active', true)
            ->groupBy('circles.id', 'circles.name', 'circles.description', 'teachers.name')
            ->having('total_sessions', '>=', 5)
            ->orderByDesc('avg_points')
            ->orderByDesc('attendance_percentage')
            ->limit(5)
            ->get();
    }

    /**
     * أفضل 5 معلمين من حيث الأداء
     */
    public function getTopPerformingTeachers()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Teacher::select('teachers.id', 'teachers.name', 'teachers.email', 'teachers.phone')
            ->selectRaw('COUNT(DISTINCT circles.id) as circles_count')
            ->selectRaw('COUNT(DISTINCT attendance.student_id) as active_students')
            ->selectRaw('COUNT(attendance.id) as total_sessions')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points')
            ->selectRaw('ROUND((SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100, 1) as attendance_percentage')
            ->selectRaw('ROUND(AVG(attendance.memorization_points), 1) as avg_memorization')
            ->join('circles', 'teachers.id', '=', 'circles.teacher_id')
            ->join('students', 'circles.id', '=', 'students.circle_id')
            ->join('attendance', 'students.id', '=', 'attendance.student_id')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.session_date', '>=', $thirtyDaysAgo)
            ->where('circles.is_active', true)
            ->groupBy('teachers.id', 'teachers.name', 'teachers.email', 'teachers.phone')
            ->having('total_sessions', '>=', 5)
            ->orderByDesc('avg_points')
            ->orderByDesc('attendance_percentage')
            ->limit(5)
            ->get();
    }

    /**
     * إحصائيات الأداء اليومية
     */
    public function getDailyPerformanceStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        return [
            'today' => $this->getPerformanceStatsForDate($today),
            'yesterday' => $this->getPerformanceStatsForDate($yesterday),
            'this_week' => $this->getPerformanceStatsForPeriod($thisWeek, Carbon::now()),
            'last_week' => $this->getPerformanceStatsForPeriod($lastWeek, $lastWeek->copy()->endOfWeek()),
        ];
    }

    /**
     * إحصائيات الأداء لتاريخ محدد
     */
    private function getPerformanceStatsForDate($date)
    {
        $stats = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereDate('class_sessions.session_date', $date)
            ->selectRaw('
                COUNT(*) as total_records,
                COUNT(DISTINCT attendance.student_id) as unique_students,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                ROUND(AVG(attendance.final_points), 1) as avg_points,
                SUM(attendance.final_points) as total_points,
                ROUND(AVG(attendance.memorization_points), 1) as avg_memorization
            ')
            ->first();

        if (!$stats || $stats->total_records == 0) {
            return [
                'sessions' => 0,
                'students' => 0,
                'attendance_rate' => 0,
                'avg_points' => 0,
                'total_points' => 0,
                'avg_memorization' => 0
            ];
        }

        return [
            'sessions' => $stats->total_records,
            'students' => $stats->unique_students,
            'present' => $stats->present_count,
            'late' => $stats->late_count,
            'absent' => $stats->absent_count,
            'attendance_rate' => $stats->total_records > 0 ? round(($stats->present_count / $stats->total_records) * 100, 1) : 0,
            'avg_points' => $stats->avg_points ?? 0,
            'total_points' => $stats->total_points ?? 0,
            'avg_memorization' => $stats->avg_memorization ?? 0
        ];
    }

    /**
     * إحصائيات الأداء لفترة محددة
     */
    private function getPerformanceStatsForPeriod($startDate, $endDate)
    {
        $stats = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_records,
                COUNT(DISTINCT attendance.student_id) as unique_students,
                COUNT(DISTINCT class_sessions.id) as unique_sessions,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                ROUND(AVG(attendance.final_points), 1) as avg_points,
                SUM(attendance.final_points) as total_points,
                ROUND(AVG(attendance.memorization_points), 1) as avg_memorization
            ')
            ->first();

        if (!$stats || $stats->total_records == 0) {
            return [
                'sessions' => 0,
                'students' => 0,
                'attendance_rate' => 0,
                'avg_points' => 0,
                'total_points' => 0,
                'avg_memorization' => 0
            ];
        }

        return [
            'sessions' => $stats->unique_sessions,
            'students' => $stats->unique_students,
            'total_records' => $stats->total_records,
            'present' => $stats->present_count,
            'late' => $stats->late_count,
            'absent' => $stats->absent_count,
            'attendance_rate' => $stats->total_records > 0 ? round(($stats->present_count / $stats->total_records) * 100, 1) : 0,
            'avg_points' => $stats->avg_points ?? 0,
            'total_points' => $stats->total_points ?? 0,
            'avg_memorization' => $stats->avg_memorization ?? 0
        ];
    }

    /**
     * الطلاب الأكثر تحسناً هذا الأسبوع
     */
    public function getMostImprovedStudentsThisWeek()
    {
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = $lastWeek->copy()->endOfWeek();

        // نقاط هذا الأسبوع
        $thisWeekPoints = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$thisWeek, Carbon::now()])
            ->select('attendance.student_id')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points_this_week')
            ->selectRaw('COUNT(*) as sessions_this_week')
            ->groupBy('attendance.student_id')
            ->having('sessions_this_week', '>=', 2);

        // نقاط الأسبوع الماضي
        $lastWeekPoints = DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->whereBetween('class_sessions.session_date', [$lastWeek, $lastWeekEnd])
            ->select('attendance.student_id')
            ->selectRaw('ROUND(AVG(attendance.final_points), 1) as avg_points_last_week')
            ->selectRaw('COUNT(*) as sessions_last_week')
            ->groupBy('attendance.student_id')
            ->having('sessions_last_week', '>=', 2);

        return Student::select('students.id', 'students.name', 'students.phone', 'circles.name as circle_name')
            ->selectRaw('tw.avg_points_this_week')
            ->selectRaw('lw.avg_points_last_week')
            ->selectRaw('(tw.avg_points_this_week - lw.avg_points_last_week) as improvement')
            ->selectRaw('tw.sessions_this_week')
            ->selectRaw('lw.sessions_last_week')
            ->joinSub($thisWeekPoints, 'tw', function ($join) {
                $join->on('students.id', '=', 'tw.student_id');
            })
            ->joinSub($lastWeekPoints, 'lw', function ($join) {
                $join->on('students.id', '=', 'lw.student_id');
            })
            ->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
            ->whereRaw('tw.avg_points_this_week > lw.avg_points_last_week')
            ->orderByDesc('improvement')
            ->limit(5)
            ->get();
    }
}
