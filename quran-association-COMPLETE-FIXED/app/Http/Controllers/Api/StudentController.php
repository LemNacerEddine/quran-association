<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\MemorizationPoint;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Get all students (Admin only)
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $circleId = $request->get('circle_id');
        $status = $request->get('status');

        $query = Student::with(['circle', 'parent']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('parent_phone', 'like', '%' . $search . '%');
        }

        if ($circleId) {
            $query->where('circle_id', $circleId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $students = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Store a new student (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:users,id',
            'age' => 'required|integer|min:3|max:100',
            'phone' => 'nullable|string|max:20',
            'parent_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'education_level' => 'required|string',
            'circle_id' => 'required|exists:circles,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الطالب بنجاح',
            'data' => $student->load(['circle', 'parent'])
        ], 201);
    }

    /**
     * Show a specific student (Admin only)
     */
    public function show(Student $student)
    {
        $student->load(['circle', 'parent', 'progress', 'attendances', 'memorizationPoints']);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update a student (Admin only)
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'sometimes|nullable|exists:users,id',
            'age' => 'sometimes|integer|min:3|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
            'parent_phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female',
            'education_level' => 'sometimes|string',
            'circle_id' => 'sometimes|exists:circles,id',
            'notes' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:active,inactive,transferred,graduated',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الطالب بنجاح',
            'data' => $student->load(['circle', 'parent'])
        ]);
    }

    /**
     * Delete a student (Admin only)
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطالب بنجاح'
        ]);
    }

    /**
     * Get student progress (Admin only)
     */
    public function getProgress(Request $request, Student $student)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $progress = StudentProgress::where('student_id', $student->id)
            ->whereBetween('test_date', [$startDate, $endDate])
            ->orderBy('test_date', 'desc')
            ->get();

        $summary = [
            'total_completed' => $progress->where('status', 'completed')->count(),
            'total_reviewing' => $progress->where('status', 'reviewing')->count(),
            'total_in_progress' => $progress->where('status', 'in_progress')->count(),
            'average_grade' => round($progress->where('status', 'completed')->avg('grade'), 2),
            'last_test_date' => $progress->first()?->test_date,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'progress' => $progress,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get student attendance (Admin only)
     */
    public function getAttendance(Request $request, Student $student)
    {
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
                'student' => $student,
                'attendance' => $attendance,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get student statistics (Admin only)
     */
    public function getStatistics(Request $request, Student $student)
    {
        $thisMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');

        // Memorization statistics
        $thisMonthPoints = MemorizationPoint::where('student_id', $student->id)
            ->where('date', 'like', $thisMonth . '%')
            ->get();

        $lastMonthPoints = MemorizationPoint::where('student_id', $student->id)
            ->where('date', 'like', $lastMonth . '%')
            ->get();

        // Attendance statistics
        $thisMonthAttendance = Attendance::where('student_id', $student->id)
            ->where('date', 'like', $thisMonth . '%')
            ->get();

        $lastMonthAttendance = Attendance::where('student_id', $student->id)
            ->where('date', 'like', $lastMonth . '%')
            ->get();

        $statistics = [
            'memorization' => [
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
                    'total_points' => $student->total_memorization_points,
                    'total_sessions' => $student->memorizationPoints()->count(),
                    'average_points' => round($student->memorizationPoints()->avg('points'), 2),
                ]
            ],
            'attendance' => [
                'this_month' => [
                    'total_days' => $thisMonthAttendance->count(),
                    'present_days' => $thisMonthAttendance->where('status', 'present')->count(),
                    'attendance_rate' => $thisMonthAttendance->count() > 0 ? 
                        round(($thisMonthAttendance->where('status', 'present')->count() / $thisMonthAttendance->count()) * 100, 2) : 0
                ],
                'last_month' => [
                    'total_days' => $lastMonthAttendance->count(),
                    'present_days' => $lastMonthAttendance->where('status', 'present')->count(),
                    'attendance_rate' => $lastMonthAttendance->count() > 0 ? 
                        round(($lastMonthAttendance->where('status', 'present')->count() / $lastMonthAttendance->count()) * 100, 2) : 0
                ],
                'all_time' => [
                    'total_days' => $student->attendances()->count(),
                    'present_days' => $student->attendances()->where('status', 'present')->count(),
                    'attendance_rate' => $student->attendances()->count() > 0 ? 
                        round(($student->attendances()->where('status', 'present')->count() / $student->attendances()->count()) * 100, 2) : 0
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'statistics' => $statistics
            ]
        ]);
    }
}

