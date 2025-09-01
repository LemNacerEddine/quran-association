<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CircleController extends Controller
{
    /**
     * Get all circles
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $teacherId = $request->get('teacher_id');
        $isActive = $request->get('is_active');

        $query = Circle::with(['teacher']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
        }

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $circles = $query->orderBy('name')->paginate($perPage);

        // إضافة عدد الطلاب لكل حلقة
        $circles->getCollection()->transform(function ($circle) {
            $circle->students_count = $circle->students()->count();
            $circle->available_spots = $circle->max_students - $circle->students_count;
            return $circle;
        });

        return response()->json([
            'success' => true,
            'data' => $circles
        ]);
    }

    /**
     * Store a new circle (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'max_students' => 'required|integer|min:1|max:50',
            'schedule_days' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $circle = Circle::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحلقة بنجاح',
            'data' => $circle->load('teacher')
        ], 201);
    }

    /**
     * Show a specific circle
     */
    public function show(Circle $circle)
    {
        $circle->load(['teacher', 'students']);
        $circle->students_count = $circle->students()->count();
        $circle->available_spots = $circle->max_students - $circle->students_count;

        return response()->json([
            'success' => true,
            'data' => $circle
        ]);
    }

    /**
     * Update a circle (Admin only)
     */
    public function update(Request $request, Circle $circle)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'max_students' => 'sometimes|integer|min:1|max:50',
            'schedule_days' => 'sometimes|string',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'location' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $circle->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحلقة بنجاح',
            'data' => $circle->load('teacher')
        ]);
    }

    /**
     * Delete a circle (Admin only)
     */
    public function destroy(Circle $circle)
    {
        // التحقق من عدم وجود طلاب في الحلقة
        if ($circle->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الحلقة لوجود طلاب مسجلين بها'
            ], 409);
        }

        $circle->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الحلقة بنجاح'
        ]);
    }

    /**
     * Get circle students
     */
    public function getStudents(Request $request, Circle $circle)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');

        $query = $circle->students()->with(['parent']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('parent_phone', 'like', '%' . $search . '%');
        }

        $students = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'circle' => $circle,
                'students' => $students
            ]
        ]);
    }

    /**
     * Get circle statistics
     */
    public function getStatistics(Circle $circle)
    {
        $studentsCount = $circle->students()->count();
        $activeStudentsCount = $circle->students()->where('is_active', true)->count();
        
        $thisMonth = now()->format('Y-m');
        
        // إحصائيات الحضور لهذا الشهر
        $attendanceStats = $circle->students()
            ->join('attendances', 'students.id', '=', 'attendances.student_id')
            ->where('attendances.date', 'like', $thisMonth . '%')
            ->selectRaw('attendances.status, count(*) as count')
            ->groupBy('attendances.status')
            ->pluck('count', 'status');

        // إحصائيات النقاط لهذا الشهر
        $pointsStats = $circle->students()
            ->join('memorization_points', 'students.id', '=', 'memorization_points.student_id')
            ->where('memorization_points.date', 'like', $thisMonth . '%')
            ->selectRaw('sum(memorization_points.points) as total_points, avg(memorization_points.points) as avg_points, count(*) as sessions')
            ->first();

        $statistics = [
            'students' => [
                'total' => $studentsCount,
                'active' => $activeStudentsCount,
                'available_spots' => $circle->max_students - $studentsCount,
                'capacity_percentage' => $circle->max_students > 0 ? 
                    round(($studentsCount / $circle->max_students) * 100, 2) : 0
            ],
            'attendance_this_month' => [
                'present' => $attendanceStats['present'] ?? 0,
                'absent' => $attendanceStats['absent'] ?? 0,
                'late' => $attendanceStats['late'] ?? 0,
                'total' => $attendanceStats->sum()
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
                'circle' => $circle,
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Get available circles for enrollment
     */
    public function getAvailable(Request $request)
    {
        $circles = Circle::with(['teacher'])
            ->where('is_active', true)
            ->get()
            ->map(function ($circle) {
                $studentsCount = $circle->students()->count();
                $circle->students_count = $studentsCount;
                $circle->available_spots = $circle->max_students - $studentsCount;
                $circle->is_full = $studentsCount >= $circle->max_students;
                return $circle;
            })
            ->filter(function ($circle) {
                return !$circle->is_full;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $circles
        ]);
    }

    /**
     * Enroll student in circle (Admin only)
     */
    public function enrollStudent(Request $request, Circle $circle)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::find($request->student_id);

        // التحقق من أن الطالب غير مسجل في حلقة أخرى
        if ($student->circle_id && $student->circle_id !== $circle->id) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب مسجل بالفعل في حلقة أخرى'
            ], 409);
        }

        // التحقق من توفر مقاعد في الحلقة
        $studentsCount = $circle->students()->count();
        if ($studentsCount >= $circle->max_students) {
            return response()->json([
                'success' => false,
                'message' => 'الحلقة ممتلئة'
            ], 409);
        }

        $student->update(['circle_id' => $circle->id]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الطالب في الحلقة بنجاح',
            'data' => $student->load(['circle', 'parent'])
        ]);
    }

    /**
     * Remove student from circle (Admin only)
     */
    public function removeStudent(Request $request, Circle $circle)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::find($request->student_id);

        // التحقق من أن الطالب مسجل في هذه الحلقة
        if ($student->circle_id !== $circle->id) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير مسجل في هذه الحلقة'
            ], 409);
        }

        $student->update(['circle_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء تسجيل الطالب من الحلقة بنجاح',
            'data' => $student->load('parent')
        ]);
    }
}

