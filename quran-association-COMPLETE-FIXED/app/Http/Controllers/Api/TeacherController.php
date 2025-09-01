<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Get all teachers
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $isActive = $request->get('is_active');
        $gender = $request->get('gender');

        $query = Teacher::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('specialization', 'like', '%' . $search . '%');
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        $teachers = $query->orderBy('name')->paginate($perPage);

        // إضافة إحصائيات لكل معلم
        $teachers->getCollection()->transform(function ($teacher) {
            $teacher->circles_count = $teacher->circles()->count();
            $teacher->students_count = $teacher->circles()->withCount('students')->get()->sum('students_count');
            return $teacher;
        });

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /**
     * Store a new teacher (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:teachers',
            'email' => 'nullable|email|unique:teachers',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'qualification' => 'required|string',
            'experience' => 'nullable|string',
            'specialization' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = Teacher::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المعلم بنجاح',
            'data' => $teacher
        ], 201);
    }

    /**
     * Show a specific teacher
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['circles', 'circles.students']);
        $teacher->circles_count = $teacher->circles()->count();
        $teacher->students_count = $teacher->circles()->withCount('students')->get()->sum('students_count');

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    /**
     * Update a teacher (Admin only)
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:teachers,phone,' . $teacher->id,
            'email' => 'sometimes|nullable|email|unique:teachers,email,' . $teacher->id,
            'address' => 'sometimes|string',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female',
            'qualification' => 'sometimes|string',
            'experience' => 'sometimes|nullable|string',
            'specialization' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المعلم بنجاح',
            'data' => $teacher
        ]);
    }

    /**
     * Delete a teacher (Admin only)
     */
    public function destroy(Teacher $teacher)
    {
        // التحقق من عدم وجود حلقات مرتبطة بالمعلم
        if ($teacher->circles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المعلم لوجود حلقات مرتبطة به'
            ], 409);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المعلم بنجاح'
        ]);
    }

    /**
     * Get teacher circles
     */
    public function getCircles(Request $request, Teacher $teacher)
    {
        $perPage = $request->get('per_page', 20);
        $isActive = $request->get('is_active');

        $query = $teacher->circles();

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $circles = $query->withCount('students')->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $teacher,
                'circles' => $circles
            ]
        ]);
    }

    /**
     * Get teacher students
     */
    public function getStudents(Request $request, Teacher $teacher)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $circleId = $request->get('circle_id');

        $query = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with(['circle', 'parent']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('parent_phone', 'like', '%' . $search . '%');
        }

        if ($circleId) {
            $query->where('circle_id', $circleId);
        }

        $students = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $teacher,
                'students' => $students
            ]
        ]);
    }

    /**
     * Get teacher statistics
     */
    public function getStatistics(Teacher $teacher)
    {
        $circlesCount = $teacher->circles()->count();
        $activeCirclesCount = $teacher->circles()->where('is_active', true)->count();
        
        $studentsCount = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->count();
        
        $activeStudentsCount = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->where('is_active', true)->count();

        $thisMonth = now()->format('Y-m');
        
        // إحصائيات الحضور لهذا الشهر
        $attendanceStats = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->join('attendances', 'students.id', '=', 'attendances.student_id')
        ->where('attendances.date', 'like', $thisMonth . '%')
        ->selectRaw('attendances.status, count(*) as count')
        ->groupBy('attendances.status')
        ->pluck('count', 'status');

        // إحصائيات النقاط لهذا الشهر
        $pointsStats = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->join('memorization_points', 'students.id', '=', 'memorization_points.student_id')
        ->where('memorization_points.date', 'like', $thisMonth . '%')
        ->selectRaw('sum(memorization_points.points) as total_points, avg(memorization_points.points) as avg_points, count(*) as sessions')
        ->first();

        $statistics = [
            'circles' => [
                'total' => $circlesCount,
                'active' => $activeCirclesCount,
            ],
            'students' => [
                'total' => $studentsCount,
                'active' => $activeStudentsCount,
            ],
            'attendance_this_month' => [
                'present' => $attendanceStats['present'] ?? 0,
                'absent' => $attendanceStats['absent'] ?? 0,
                'late' => $attendanceStats['late'] ?? 0,
                'total' => $attendanceStats->sum(),
                'attendance_rate' => $attendanceStats->sum() > 0 ? 
                    round((($attendanceStats['present'] ?? 0) / $attendanceStats->sum()) * 100, 2) : 0
            ],
            'memorization_this_month' => [
                'total_points' => $pointsStats->total_points ?? 0,
                'average_points' => round($pointsStats->avg_points ?? 0, 2),
                'total_sessions' => $pointsStats->sessions ?? 0
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $teacher,
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Get teacher performance report
     */
    public function getPerformanceReport(Request $request, Teacher $teacher)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $studentIds = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->pluck('id');

        // إحصائيات الحضور للفترة المحددة
        $attendanceData = \DB::table('attendances')
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // إحصائيات النقاط للفترة المحددة
        $pointsData = \DB::table('memorization_points')
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('sum(points) as total_points, avg(points) as avg_points, count(*) as sessions')
            ->first();

        // أفضل الطلاب في الحفظ
        $topStudents = Student::whereHas('circle', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->withSum(['memorizationPoints' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        }], 'points')
        ->orderByDesc('memorization_points_sum_points')
        ->limit(5)
        ->get();

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'attendance' => [
                'present' => $attendanceData['present'] ?? 0,
                'absent' => $attendanceData['absent'] ?? 0,
                'late' => $attendanceData['late'] ?? 0,
                'total' => $attendanceData->sum(),
                'attendance_rate' => $attendanceData->sum() > 0 ? 
                    round((($attendanceData['present'] ?? 0) / $attendanceData->sum()) * 100, 2) : 0
            ],
            'memorization' => [
                'total_points' => $pointsData->total_points ?? 0,
                'average_points' => round($pointsData->avg_points ?? 0, 2),
                'total_sessions' => $pointsData->sessions ?? 0
            ],
            'top_students' => $topStudents
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $teacher,
                'report' => $report
            ]
        ]);
    }
}

