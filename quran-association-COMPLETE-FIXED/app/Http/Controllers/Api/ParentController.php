<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\MemorizationPoint;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentController extends Controller
{
    /**
     * Get all children for the authenticated parent
     */
    public function getChildren(Request $request)
    {
        $parent = $request->user();
        
        if (!$parent->isParent()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول'
            ], 403);
        }

        $children = $parent->children()->with(['circle', 'circle.teacher'])->get();

        return response()->json([
            'success' => true,
            'data' => $children
        ]);
    }

    /**
     * Get child progress
     */
    public function getChildProgress(Request $request, Student $student)
    {
        $parent = $request->user();
        
        // التحقق من أن الطالب ينتمي لولي الأمر
        if ($student->parent_id !== $parent->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
            ], 403);
        }

        $progress = StudentProgress::where('student_id', $student->id)
            ->orderBy('test_date', 'desc')
            ->get();

        $summary = [
            'total_completed' => $progress->where('status', 'completed')->count(),
            'total_reviewing' => $progress->where('status', 'reviewing')->count(),
            'total_in_progress' => $progress->where('status', 'in_progress')->count(),
            'average_grade' => $progress->where('status', 'completed')->avg('grade'),
            'last_test_date' => $progress->first()?->test_date,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'progress' => $progress,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get child attendance
     */
    public function getChildAttendance(Request $request, Student $student)
    {
        $parent = $request->user();
        
        // التحقق من أن الطالب ينتمي لولي الأمر
        if ($student->parent_id !== $parent->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
            ], 403);
        }

        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $attendance = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_days' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'attendance_rate' => $attendance->count() > 0 ? 
                round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get child memorization points
     */
    public function getChildMemorization(Request $request, Student $student)
    {
        $parent = $request->user();
        
        // التحقق من أن الطالب ينتمي لولي الأمر
        if ($student->parent_id !== $parent->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
            ], 403);
        }

        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $memorization = MemorizationPoint::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_sessions' => $memorization->count(),
            'total_points' => $memorization->sum('points'),
            'average_points' => $memorization->avg('points'),
            'morning_sessions' => $memorization->where('session_type', 'morning')->count(),
            'evening_sessions' => $memorization->where('session_type', 'evening')->count(),
            'best_score' => $memorization->max('points'),
            'recent_trend' => $this->getRecentTrend($memorization)
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'memorization' => $memorization,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get parent dashboard data
     */
    public function getDashboard(Request $request)
    {
        $parent = $request->user();
        
        if (!$parent->isParent()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول'
            ], 403);
        }

        $children = $parent->children()->with(['circle'])->get();
        $childrenIds = $children->pluck('id');

        // إحصائيات عامة
        $totalChildren = $children->count();
        $activeChildren = $children->where('is_active', true)->count();

        // إحصائيات الحضور لهذا الشهر
        $thisMonth = now()->format('Y-m');
        $attendanceStats = Attendance::whereIn('student_id', $childrenIds)
            ->where('date', 'like', $thisMonth . '%')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // إحصائيات النقاط لهذا الشهر
        $pointsStats = MemorizationPoint::whereIn('student_id', $childrenIds)
            ->where('date', 'like', $thisMonth . '%')
            ->select(DB::raw('sum(points) as total_points, avg(points) as avg_points, count(*) as sessions'))
            ->first();

        // آخر النشاطات
        $recentActivities = collect();
        
        // آخر نقاط الحفظ
        $recentMemorization = MemorizationPoint::whereIn('student_id', $childrenIds)
            ->with('student')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($point) {
                return [
                    'type' => 'memorization',
                    'student_name' => $point->student->name,
                    'points' => $point->points,
                    'date' => $point->date,
                    'content' => $point->memorized_content
                ];
            });

        // آخر الحضور
        $recentAttendance = Attendance::whereIn('student_id', $childrenIds)
            ->with('student')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($attendance) {
                return [
                    'type' => 'attendance',
                    'student_name' => $attendance->student->name,
                    'status' => $attendance->status,
                    'date' => $attendance->date
                ];
            });

        $recentActivities = $recentMemorization->merge($recentAttendance)
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'children' => $children,
                'stats' => [
                    'total_children' => $totalChildren,
                    'active_children' => $activeChildren,
                    'attendance' => $attendanceStats,
                    'points' => $pointsStats
                ],
                'recent_activities' => $recentActivities
            ]
        ]);
    }

    /**
     * Get recent trend for memorization points
     */
    private function getRecentTrend($memorization)
    {
        $recent = $memorization->take(5);
        if ($recent->count() < 2) {
            return 'stable';
        }

        $firstHalf = $recent->take(ceil($recent->count() / 2))->avg('points');
        $secondHalf = $recent->skip(ceil($recent->count() / 2))->avg('points');

        if ($firstHalf > $secondHalf + 0.5) {
            return 'improving';
        } elseif ($secondHalf > $firstHalf + 0.5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
}

