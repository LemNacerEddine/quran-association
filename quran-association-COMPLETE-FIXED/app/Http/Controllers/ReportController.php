<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\Attendance;
use App\Models\StudentProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // General statistics
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::count(),
            'active_teachers' => Teacher::where('is_active', true)->count(),
            'total_circles' => Circle::count(),
            'active_circles' => Circle::where('is_active', true)->count(),
            'total_attendances_today' => Attendance::whereDate('attendance_date', Carbon::today())->count(),
        ];

        // Monthly attendance statistics
        $monthlyAttendance = Attendance::select(
            DB::raw('MONTH(attendance_date) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
            DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent')
        )
        ->whereYear('attendance_date', Carbon::now()->year)
        ->groupBy(DB::raw('MONTH(attendance_date)'))
        ->get();

        // Top performing circles
        $topCircles = Circle::withCount(['students', 'attendances'])
            ->with('teacher')
            ->where('is_active', true)
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get();

        return view('reports.simple_index', compact('stats', 'monthlyAttendance', 'topCircles'))->with('statistics', $stats);
    }

    public function attendance(Request $request)
    {
        $circle_id = $request->get('circle_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $circles = Circle::where('is_active', true)->get();
        
        $attendanceData = collect();
        $attendanceStats = [];
        
        if ($circle_id) {
            $circle = Circle::findOrFail($circle_id);
            
            // Get attendance data
            $attendanceData = Attendance::with(['student'])
                ->where('circle_id', $circle_id)
                ->whereBetween('attendance_date', [$start_date, $end_date])
                ->orderBy('attendance_date', 'desc')
                ->get();

            // Calculate statistics
            $totalSessions = $attendanceData->groupBy('attendance_date')->count();
            $totalAttendances = $attendanceData->count();
            $presentCount = $attendanceData->where('status', 'present')->count();
            $absentCount = $attendanceData->where('status', 'absent')->count();
            
            $attendanceStats = [
                'total_sessions' => $totalSessions,
                'total_attendances' => $totalAttendances,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'attendance_rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0,
            ];
        }

        return view('reports.attendance', compact('circles', 'attendanceData', 'attendanceStats', 'circle_id', 'start_date', 'end_date'));
    }

    public function students(Request $request)
    {
        $circle_id = $request->get('circle_id');
        $circles = Circle::where('is_active', true)->get();
        
        $studentsData = collect();
        
        if ($circle_id) {
            $studentsData = Student::with(['circle', 'attendances', 'progress'])
                ->where('circle_id', $circle_id)
                ->where('is_active', true)
                ->get()
                ->map(function ($student) {
                    $totalAttendances = $student->attendances->count();
                    $presentCount = $student->attendances->where('status', 'present')->count();
                    $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0;
                    
                    $averagePoints = $student->progress->avg('points') ?? 0;
                    
                    return [
                        'student' => $student,
                        'total_attendances' => $totalAttendances,
                        'present_count' => $presentCount,
                        'attendance_rate' => $attendanceRate,
                        'average_points' => round($averagePoints, 2),
                        'total_progress' => $student->progress->count(),
                    ];
                });
        }

        return view('reports.students', compact('circles', 'studentsData', 'circle_id'));
    }

    public function progress(Request $request)
    {
        $circle_id = $request->get('circle_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $circles = Circle::where('is_active', true)->get();
        
        $progressData = collect();
        $progressStats = [];
        
        if ($circle_id) {
            $progressData = StudentProgress::with(['student'])
                ->whereHas('student', function($query) use ($circle_id) {
                    $query->where('circle_id', $circle_id);
                })
                ->whereBetween('attendance_date', [$start_date, $end_date])
                ->orderBy('attendance_date', 'desc')
                ->get();

            // Calculate statistics
            $totalEntries = $progressData->count();
            $averagePoints = $progressData->avg('points') ?? 0;
            $highPerformers = $progressData->where('points', '>=', 8)->count();
            $lowPerformers = $progressData->where('points', '<=', 3)->count();
            
            $progressStats = [
                'total_entries' => $totalEntries,
                'average_points' => round($averagePoints, 2),
                'high_performers' => $highPerformers,
                'low_performers' => $lowPerformers,
                'high_performance_rate' => $totalEntries > 0 ? round(($highPerformers / $totalEntries) * 100, 2) : 0,
            ];
        }

        return view('reports.progress', compact('circles', 'progressData', 'progressStats', 'circle_id', 'start_date', 'end_date'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'attendance');
        $circle_id = $request->get('circle_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // This would typically generate CSV/Excel files
        // For now, we'll redirect back with a success message
        return redirect()->back()->with('success', 'سيتم إضافة ميزة التصدير قريباً');
    }
}
