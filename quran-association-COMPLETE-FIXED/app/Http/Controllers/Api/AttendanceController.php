<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Get attendance records (Admin only)
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $date = $request->get('attendance_date');
        $circleId = $request->get('circle_id');
        $studentId = $request->get('student_id');
        $status = $request->get('status');

        $query = Attendance::with(['student', 'student.circle']);

        if ($date) {
            $query->where('attendance_date', $date);
        }

        if ($circleId) {
            $query->whereHas('student', function ($q) use ($circleId) {
                $q->where('circle_id', $circleId);
            });
        }

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    /**
     * Record attendance for a student (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من عدم وجود سجل حضور لنفس الطالب في نفس اليوم
        $existingAttendance = Attendance::where('student_id', $request->student_id)
            ->where('attendance_date', $request->date)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل الحضور لهذا الطالب في هذا اليوم مسبقاً'
            ], 409);
        }

        $attendance = Attendance::create([
            'student_id' => $request->student_id,
            'attendance_date' => $request->date,
            'status' => $request->status,
            'notes' => $request->notes,
            'recorded_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الحضور بنجاح',
            'data' => $attendance->load(['student', 'student.circle'])
        ], 201);
    }

    /**
     * Update attendance record (Admin only)
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:present,absent,late',
            'notes' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $attendance->update($request->only(['status', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث سجل الحضور بنجاح',
            'data' => $attendance->load(['student', 'student.circle'])
        ]);
    }

    /**
     * Delete attendance record (Admin only)
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف سجل الحضور بنجاح'
        ]);
    }

    /**
     * Record bulk attendance for a circle (Admin only)
     */
    public function recordBulkAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'circle_id' => 'required|exists:circles,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $circle = Circle::find($request->circle_id);
        $date = $request->date;
        $attendanceData = $request->attendance;

        // التحقق من أن جميع الطلاب ينتمون للحلقة المحددة
        $studentIds = collect($attendanceData)->pluck('student_id');
        $circleStudentIds = $circle->students()->pluck('id');
        
        $invalidStudents = $studentIds->diff($circleStudentIds);
        if ($invalidStudents->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'بعض الطلاب لا ينتمون للحلقة المحددة'
            ], 422);
        }

        // التحقق من عدم وجود سجلات حضور لنفس اليوم
        $existingAttendance = Attendance::whereIn('student_id', $studentIds)
            ->where('attendance_date', $date)
            ->exists();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل الحضور لبعض الطلاب في هذا اليوم مسبقاً'
            ], 409);
        }

        $attendanceRecords = [];
        foreach ($attendanceData as $record) {
            $attendanceRecords[] = [
                'student_id' => $record['student_id'],
                'attendance_date' => $date,
                'status' => $record['status'],
                'notes' => $record['notes'] ?? null,
                'recorded_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Attendance::insert($attendanceRecords);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الحضور للحلقة بنجاح',
            'data' => [
                'circle' => $circle,
                'attendance_date' => $date,
                'recorded_count' => count($attendanceRecords)
            ]
        ], 201);
    }

    /**
     * Get attendance for a specific date and circle (Admin only)
     */
    public function getCircleAttendance(Request $request, Circle $circle)
    {
        $date = $request->get('attendance_date', now()->format('Y-m-d'));

        $students = $circle->students()->with(['attendances' => function ($q) use ($date) {
            $q->where('attendance_date', $date);
        }])->get();

        $attendanceData = $students->map(function ($student) use ($date) {
            $attendance = $student->attendances->first();
            return [
                'student' => $student,
                'attendance' => $attendance,
                'status' => $attendance ? $attendance->status : 'not_recorded',
                'notes' => $attendance ? $attendance->notes : null,
            ];
        });

        $summary = [
            'total_students' => $students->count(),
            'present' => $attendanceData->where('status', 'present')->count(),
            'absent' => $attendanceData->where('status', 'absent')->count(),
            'late' => $attendanceData->where('status', 'late')->count(),
            'not_recorded' => $attendanceData->where('status', 'not_recorded')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'circle' => $circle,
                'attendance_date' => $date,
                'attendance' => $attendanceData,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get student attendance history (Admin only)
     */
    public function getStudentAttendance(Request $request, Student $student)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $attendance = $student->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
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
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'attendance' => $attendance,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get attendance statistics (Admin only)
     */
    public function getStatistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $circleId = $request->get('circle_id');

        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);

        if ($circleId) {
            $query->whereHas('student', function ($q) use ($circleId) {
                $q->where('circle_id', $circleId);
            });
        }

        $attendanceData = $query->get();

        $statistics = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'overall' => [
                'total_records' => $attendanceData->count(),
                'present' => $attendanceData->where('status', 'present')->count(),
                'absent' => $attendanceData->where('status', 'absent')->count(),
                'late' => $attendanceData->where('status', 'late')->count(),
                'attendance_rate' => $attendanceData->count() > 0 ? 
                    round(($attendanceData->where('status', 'present')->count() / $attendanceData->count()) * 100, 2) : 0
            ],
            'daily_breakdown' => $attendanceData->groupBy('attendance_date')->map(function ($dayAttendance, $date) {
                return [
                    'attendance_date' => $date,
                    'total' => $dayAttendance->count(),
                    'present' => $dayAttendance->where('status', 'present')->count(),
                    'absent' => $dayAttendance->where('status', 'absent')->count(),
                    'late' => $dayAttendance->where('status', 'late')->count(),
                    'attendance_rate' => $dayAttendance->count() > 0 ? 
                        round(($dayAttendance->where('status', 'present')->count() / $dayAttendance->count()) * 100, 2) : 0
                ];
            })->values()
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Get attendance report for a specific period (Admin only)
     */
    public function getReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $circleId = $request->get('circle_id');

        $query = Student::with(['circle', 'parent']);

        if ($circleId) {
            $query->where('circle_id', $circleId);
        }

        $students = $query->get();

        $report = $students->map(function ($student) use ($startDate, $endDate) {
            $attendance = $student->attendances()
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            return [
                'student' => $student,
                'attendance_summary' => [
                    'total_days' => $attendance->count(),
                    'present_days' => $attendance->where('status', 'present')->count(),
                    'absent_days' => $attendance->where('status', 'absent')->count(),
                    'late_days' => $attendance->where('status', 'late')->count(),
                    'attendance_rate' => $attendance->count() > 0 ? 
                        round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 2) : 0
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'report' => $report
            ]
        ]);
    }
}

