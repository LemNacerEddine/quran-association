<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemorizationPoint;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MemorizationController extends Controller
{
    /**
     * Store memorization points (Admin only)
     */
    public function storePoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'session_type' => 'required|in:morning,evening',
            'points' => 'required|integer|min:0|max:10',
            'memorized_content' => 'nullable|string',
            'teacher_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من عدم وجود نقاط مسجلة لنفس الطالب في نفس اليوم والجلسة
        $existingPoint = MemorizationPoint::where('student_id', $request->student_id)
            ->where('date', $request->date)
            ->where('session_type', $request->session_type)
            ->first();

        if ($existingPoint) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل نقاط لهذا الطالب في هذا اليوم والجلسة مسبقاً'
            ], 409);
        }

        $point = MemorizationPoint::create([
            'student_id' => $request->student_id,
            'date' => $request->date,
            'session_type' => $request->session_type,
            'points' => $request->points,
            'memorized_content' => $request->memorized_content,
            'teacher_notes' => $request->teacher_notes,
            'recorded_by' => $request->user()->id,
        ]);

        // تحديث إجمالي نقاط الطالب
        $this->updateStudentTotalPoints($request->student_id);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل النقاط بنجاح',
            'data' => $point->load('student', 'recordedBy')
        ], 201);
    }

    /**
     * Update memorization points (Admin only)
     */
    public function updatePoints(Request $request, MemorizationPoint $point)
    {
        $validator = Validator::make($request->all(), [
            'points' => 'sometimes|integer|min:0|max:10',
            'memorized_content' => 'sometimes|nullable|string',
            'teacher_notes' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $point->update($request->only(['points', 'memorized_content', 'teacher_notes']));

        // تحديث إجمالي نقاط الطالب
        $this->updateStudentTotalPoints($point->student_id);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث النقاط بنجاح',
            'data' => $point->load('student', 'recordedBy')
        ]);
    }

    /**
     * Delete memorization points (Admin only)
     */
    public function deletePoints(MemorizationPoint $point)
    {
        $studentId = $point->student_id;
        $point->delete();

        // تحديث إجمالي نقاط الطالب
        $this->updateStudentTotalPoints($studentId);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف النقاط بنجاح'
        ]);
    }

    /**
     * Get student memorization points (Admin only)
     */
    public function getStudentPoints(Request $request, Student $student)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $points = MemorizationPoint::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('recordedBy')
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_sessions' => $points->count(),
            'total_points' => $points->sum('points'),
            'average_points' => $points->avg('points'),
            'morning_sessions' => $points->where('session_type', 'morning')->count(),
            'evening_sessions' => $points->where('session_type', 'evening')->count(),
            'best_score' => $points->max('points'),
            'worst_score' => $points->min('points'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'points' => $points,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get child memorization points (Parent only)
     */
    public function getChildPoints(Request $request, Student $student)
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

        $points = MemorizationPoint::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_sessions' => $points->count(),
            'total_points' => $points->sum('points'),
            'average_points' => round($points->avg('points'), 2),
            'morning_sessions' => $points->where('session_type', 'morning')->count(),
            'evening_sessions' => $points->where('session_type', 'evening')->count(),
            'best_score' => $points->max('points'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $points,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get child memorization summary (Parent only)
     */
    public function getChildSummary(Request $request, Student $student)
    {
        $parent = $request->user();
        
        // التحقق من أن الطالب ينتمي لولي الأمر
        if ($student->parent_id !== $parent->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
            ], 403);
        }

        // إحصائيات هذا الشهر
        $thisMonth = now()->format('Y-m');
        $thisMonthPoints = MemorizationPoint::where('student_id', $student->id)
            ->where('date', 'like', $thisMonth . '%')
            ->get();

        // إحصائيات الشهر الماضي
        $lastMonth = now()->subMonth()->format('Y-m');
        $lastMonthPoints = MemorizationPoint::where('student_id', $student->id)
            ->where('date', 'like', $lastMonth . '%')
            ->get();

        // إحصائيات عامة
        $allTimePoints = MemorizationPoint::where('student_id', $student->id)->get();

        $summary = [
            'this_month' => [
                'total_sessions' => $thisMonthPoints->count(),
                'total_points' => $thisMonthPoints->sum('points'),
                'average_points' => round($thisMonthPoints->avg('points'), 2),
            ],
            'last_month' => [
                'total_sessions' => $lastMonthPoints->count(),
                'total_points' => $lastMonthPoints->sum('points'),
                'average_points' => round($lastMonthPoints->avg('points'), 2),
            ],
            'all_time' => [
                'total_sessions' => $allTimePoints->count(),
                'total_points' => $allTimePoints->sum('points'),
                'average_points' => round($allTimePoints->avg('points'), 2),
                'best_score' => $allTimePoints->max('points'),
                'first_session' => $allTimePoints->min('date'),
                'last_session' => $allTimePoints->max('date'),
            ],
            'progress_trend' => $this->calculateProgressTrend($student->id),
            'weekly_chart' => $this->getWeeklyChart($student->id),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Update student total memorization points
     */
    private function updateStudentTotalPoints($studentId)
    {
        $totalPoints = MemorizationPoint::where('student_id', $studentId)->sum('points');
        Student::where('id', $studentId)->update(['total_memorization_points' => $totalPoints]);
    }

    /**
     * Calculate progress trend
     */
    private function calculateProgressTrend($studentId)
    {
        $recentPoints = MemorizationPoint::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->pluck('points');

        if ($recentPoints->count() < 5) {
            return 'insufficient_data';
        }

        $firstHalf = $recentPoints->take(5)->avg();
        $secondHalf = $recentPoints->skip(5)->avg();

        if ($firstHalf > $secondHalf + 0.5) {
            return 'improving';
        } elseif ($secondHalf > $firstHalf + 0.5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyChart($studentId)
    {
        $weeklyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->format('l');
            
            $dayPoints = MemorizationPoint::where('student_id', $studentId)
                ->where('date', $date)
                ->sum('points');
                
            $weeklyData[] = [
                'date' => $date,
                'day' => $dayName,
                'points' => $dayPoints
            ];
        }
        
        return $weeklyData;
    }
}

