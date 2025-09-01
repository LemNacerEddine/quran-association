<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\ClassSession;
use App\Models\AttendanceSession;
use App\Models\MemorizationPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $statistics = $this->getOverallStatistics();
        return view('reports.index', compact('statistics'));
    }

    public function attendance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $circleId = $request->get('circle_id');

        $query = AttendanceSession::with(['student', 'session.schedule.circle'])
                                 ->whereBetween('created_at', [$startDate, $endDate]);

        if ($circleId) {
            $query->whereHas('session.schedule.circle', function($q) use ($circleId) {
                $q->where('id', $circleId);
            });
        }

        $attendanceData = $query->get();

        // Calculate statistics
        $totalSessions = $attendanceData->count();
        $presentSessions = $attendanceData->where('status', 'present')->count();
        $absentSessions = $attendanceData->where('status', 'absent')->count();
        $lateSessions = $attendanceData->where('status', 'late')->count();

        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 2) : 0;

        // Group by student
        $studentAttendance = $attendanceData->groupBy('student_id')->map(function($sessions) {
            $total = $sessions->count();
            $present = $sessions->where('status', 'present')->count();
            $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            
            return [
                'student' => $sessions->first()->student,
                'total_sessions' => $total,
                'present_sessions' => $present,
                'absent_sessions' => $sessions->where('status', 'absent')->count(),
                'late_sessions' => $sessions->where('status', 'late')->count(),
                'attendance_rate' => $rate
            ];
        })->sortByDesc('attendance_rate');

        // Daily attendance chart data
        $dailyAttendance = $attendanceData->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($sessions, $date) {
            return [
                'date' => $date,
                'total' => $sessions->count(),
                'present' => $sessions->where('status', 'present')->count(),
                'absent' => $sessions->where('status', 'absent')->count(),
                'rate' => $sessions->count() > 0 ? round(($sessions->where('status', 'present')->count() / $sessions->count()) * 100, 2) : 0
            ];
        })->values();

        $circles = Circle::all();

        return view('reports.attendance', compact(
            'attendanceData', 'studentAttendance', 'dailyAttendance', 'circles',
            'totalSessions', 'presentSessions', 'absentSessions', 'lateSessions', 'attendanceRate',
            'startDate', 'endDate', 'circleId'
        ));
    }

    public function memorization(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $circleId = $request->get('circle_id');

        $query = MemorizationPoint::with(['student.circle'])
                                 ->whereBetween('created_at', [$startDate, $endDate]);

        if ($circleId) {
            $query->whereHas('student.circle', function($q) use ($circleId) {
                $q->where('id', $circleId);
            });
        }

        $memorizationData = $query->get();

        // Calculate statistics
        $totalPoints = $memorizationData->sum('points');
        $averagePoints = $memorizationData->avg('points') ?? 0;
        $studentsCount = $memorizationData->pluck('student_id')->unique()->count();

        // Group by student
        $studentProgress = $memorizationData->groupBy('student_id')->map(function($points) {
            $totalPoints = $points->sum('points');
            $averageGrade = $points->avg('grade') ?? 0;
            $sessionsCount = $points->count();
            
            return [
                'student' => $points->first()->student,
                'total_points' => $totalPoints,
                'average_grade' => round($averageGrade, 2),
                'sessions_count' => $sessionsCount,
                'last_session' => $points->sortByDesc('created_at')->first()->created_at
            ];
        })->sortByDesc('total_points');

        // Monthly progress chart
        $monthlyProgress = $memorizationData->groupBy(function($item) {
            return $item->created_at->format('Y-m');
        })->map(function($points, $month) {
            return [
                'month' => $month,
                'total_points' => $points->sum('points'),
                'average_grade' => round($points->avg('grade') ?? 0, 2),
                'students_count' => $points->pluck('student_id')->unique()->count()
            ];
        })->values();

        // Surah completion statistics
        $surahStats = $memorizationData->groupBy('surah_name')->map(function($points, $surah) {
            return [
                'surah' => $surah,
                'students_count' => $points->pluck('student_id')->unique()->count(),
                'total_points' => $points->sum('points'),
                'average_grade' => round($points->avg('grade') ?? 0, 2)
            ];
        })->sortByDesc('students_count');

        $circles = Circle::all();

        return view('reports.memorization', compact(
            'memorizationData', 'studentProgress', 'monthlyProgress', 'surahStats', 'circles',
            'totalPoints', 'averagePoints', 'studentsCount',
            'startDate', 'endDate', 'circleId'
        ));
    }

    public function teachers(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $teachers = Teacher::with(['circles.students'])->get();

        $teacherStats = $teachers->map(function($teacher) use ($startDate, $endDate) {
            $circles = $teacher->circles;
            $students = $circles->flatMap->students;
            
            // Calculate attendance rate for teacher's students
            $attendanceData = AttendanceSession::whereIn('student_id', $students->pluck('id'))
                                              ->whereBetween('created_at', [$startDate, $endDate])
                                              ->get();
            
            $totalSessions = $attendanceData->count();
            $presentSessions = $attendanceData->where('status', 'present')->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 2) : 0;

            // Calculate memorization progress
            $memorizationData = MemorizationPoint::whereIn('student_id', $students->pluck('id'))
                                                ->whereBetween('created_at', [$startDate, $endDate])
                                                ->get();
            
            $totalPoints = $memorizationData->sum('points');
            $averageGrade = $memorizationData->avg('grade') ?? 0;

            return [
                'teacher' => $teacher,
                'circles_count' => $circles->count(),
                'students_count' => $students->count(),
                'attendance_rate' => $attendanceRate,
                'total_points' => $totalPoints,
                'average_grade' => round($averageGrade, 2),
                'performance_score' => $this->calculateTeacherPerformance($attendanceRate, $averageGrade, $students->count())
            ];
        })->sortByDesc('performance_score');

        return view('reports.teachers', compact('teacherStats', 'startDate', 'endDate'));
    }

    public function circles(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $circles = Circle::with(['teacher', 'students'])->get();

        $circleStats = $circles->map(function($circle) use ($startDate, $endDate) {
            $students = $circle->students;
            
            // Calculate attendance rate
            $attendanceData = AttendanceSession::whereIn('student_id', $students->pluck('id'))
                                              ->whereBetween('created_at', [$startDate, $endDate])
                                              ->get();
            
            $totalSessions = $attendanceData->count();
            $presentSessions = $attendanceData->where('status', 'present')->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 2) : 0;

            // Calculate memorization progress
            $memorizationData = MemorizationPoint::whereIn('student_id', $students->pluck('id'))
                                                ->whereBetween('created_at', [$startDate, $endDate])
                                                ->get();
            
            $totalPoints = $memorizationData->sum('points');
            $averageGrade = $memorizationData->avg('grade') ?? 0;

            return [
                'circle' => $circle,
                'students_count' => $students->count(),
                'attendance_rate' => $attendanceRate,
                'total_points' => $totalPoints,
                'average_grade' => round($averageGrade, 2),
                'efficiency_score' => $this->calculateCircleEfficiency($attendanceRate, $averageGrade, $students->count())
            ];
        })->sortByDesc('efficiency_score');

        return view('reports.circles', compact('circleStats', 'startDate', 'endDate'));
    }

    public function financial(Request $request)
    {
        // This would include financial reports if implemented
        return view('reports.financial');
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'attendance');
        $format = $request->get('format', 'pdf');

        // Implementation for exporting reports in different formats
        switch ($type) {
            case 'attendance':
                return $this->exportAttendanceReport($request, $format);
            case 'memorization':
                return $this->exportMemorizationReport($request, $format);
            case 'teachers':
                return $this->exportTeachersReport($request, $format);
            case 'circles':
                return $this->exportCirclesReport($request, $format);
            default:
                return redirect()->back()->with('error', 'نوع التقرير غير صحيح');
        }
    }

    private function getOverallStatistics()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_students' => Student::count(),
            'active_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::count(),
            'active_teachers' => Teacher::where('is_active', true)->count(),
            'total_circles' => Circle::count(),
            'active_circles' => Circle::where('is_active', true)->count(),
            'this_month_attendance' => $this->getMonthlyAttendanceRate($currentMonth),
            'last_month_attendance' => $this->getMonthlyAttendanceRate($lastMonth),
            'this_month_memorization' => $this->getMonthlyMemorizationPoints($currentMonth),
            'last_month_memorization' => $this->getMonthlyMemorizationPoints($lastMonth),
            'top_performing_circles' => $this->getTopPerformingCircles(),
            'recent_activities' => $this->getRecentActivities()
        ];
    }

    private function getMonthlyAttendanceRate($month)
    {
        $attendanceData = AttendanceSession::whereMonth('created_at', $month->month)
                                          ->whereYear('created_at', $month->year)
                                          ->get();
        
        $total = $attendanceData->count();
        $present = $attendanceData->where('status', 'present')->count();
        
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }

    private function getMonthlyMemorizationPoints($month)
    {
        return MemorizationPoint::whereMonth('created_at', $month->month)
                               ->whereYear('created_at', $month->year)
                               ->sum('points');
    }

    private function getTopPerformingCircles($limit = 5)
    {
        return Circle::with(['teacher', 'students'])
                    ->get()
                    ->map(function($circle) {
                        $students = $circle->students;
                        $attendanceRate = $this->calculateCircleAttendanceRate($circle);
                        $memorizationAvg = $this->calculateCircleMemorizationAverage($circle);
                        
                        return [
                            'circle' => $circle,
                            'performance_score' => ($attendanceRate + $memorizationAvg) / 2
                        ];
                    })
                    ->sortByDesc('performance_score')
                    ->take($limit);
    }

    private function getRecentActivities($limit = 10)
    {
        $activities = collect();

        // Recent memorization points
        $recentMemorization = MemorizationPoint::with('student')
                                              ->latest()
                                              ->take($limit / 2)
                                              ->get()
                                              ->map(function($point) {
                                                  return [
                                                      'type' => 'memorization',
                                                      'message' => "الطالب {$point->student->name} حصل على {$point->points} نقطة في {$point->surah_name}",
                                                      'created_at' => $point->created_at
                                                  ];
                                              });

        // Recent attendance
        $recentAttendance = AttendanceSession::with('student')
                                           ->where('status', 'present')
                                           ->latest()
                                           ->take($limit / 2)
                                           ->get()
                                           ->map(function($attendance) {
                                               return [
                                                   'type' => 'attendance',
                                                   'message' => "الطالب {$attendance->student->name} حضر الجلسة",
                                                   'created_at' => $attendance->created_at
                                               ];
                                           });

        return $activities->merge($recentMemorization)
                         ->merge($recentAttendance)
                         ->sortByDesc('created_at')
                         ->take($limit);
    }

    private function calculateTeacherPerformance($attendanceRate, $averageGrade, $studentsCount)
    {
        // Weighted performance calculation
        $attendanceWeight = 0.4;
        $gradeWeight = 0.4;
        $capacityWeight = 0.2;
        
        $capacityScore = min(($studentsCount / 20) * 100, 100); // Assuming max 20 students per teacher
        
        return round(
            ($attendanceRate * $attendanceWeight) + 
            (($averageGrade / 10) * 100 * $gradeWeight) + 
            ($capacityScore * $capacityWeight), 
            2
        );
    }

    private function calculateCircleEfficiency($attendanceRate, $averageGrade, $studentsCount)
    {
        return $this->calculateTeacherPerformance($attendanceRate, $averageGrade, $studentsCount);
    }

    private function calculateCircleAttendanceRate($circle)
    {
        $students = $circle->students;
        $attendanceData = AttendanceSession::whereIn('student_id', $students->pluck('id'))->get();
        
        $total = $attendanceData->count();
        $present = $attendanceData->where('status', 'present')->count();
        
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }

    private function calculateCircleMemorizationAverage($circle)
    {
        $students = $circle->students;
        return MemorizationPoint::whereIn('student_id', $students->pluck('id'))
                               ->avg('grade') ?? 0;
    }

    // Export methods would be implemented here
    private function exportAttendanceReport($request, $format)
    {
        // Implementation for exporting attendance reports
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    private function exportMemorizationReport($request, $format)
    {
        // Implementation for exporting memorization reports
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    private function exportTeachersReport($request, $format)
    {
        // Implementation for exporting teachers reports
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    private function exportCirclesReport($request, $format)
    {
        // Implementation for exporting circles reports
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}

