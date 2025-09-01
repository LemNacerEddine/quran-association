<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceMobileController extends Controller
{
    /**
     * Save attendance for a session
     */
    public function saveAttendance(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.memorization_points' => 'nullable|integer|min:0|max:5',
            'attendance.*.notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            
            // Verify session exists and user has permission
            if ($user->role === 'teacher') {
                $teacher = Teacher::where('phone', $user->phone)->first();
                $session = Session::whereHas('circle', function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->find($sessionId);
            } else {
                // Admin can access all sessions
                $session = Session::find($sessionId);
            }

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة أو غير مصرح لك بالوصول إليها'
                ], 404);
            }

            DB::beginTransaction();

            $savedAttendance = [];
            
            foreach ($request->attendance as $attendanceData) {
                $studentId = $attendanceData['student_id'];
                $status = $attendanceData['status'];
                $memorizationPoints = $attendanceData['memorization_points'] ?? 0;
                $notes = $attendanceData['notes'] ?? null;

                // Calculate attendance points based on status
                $attendancePoints = $this->calculateAttendancePoints($status, $studentId, $session->session_date);
                
                // Calculate total points
                $totalPoints = $status === 'absent' ? 0 : $attendancePoints + $memorizationPoints;

                // Get consecutive lateness count
                $consecutiveLateness = $this->getConsecutiveLateness($studentId, $session->session_date, $status);

                // Create or update attendance record
                $attendance = Attendance::updateOrCreate(
                    [
                        'session_id' => $sessionId,
                        'student_id' => $studentId
                    ],
                    [
                        'status' => $status,
                        'attendance_points' => $attendancePoints,
                        'memorization_points' => $memorizationPoints,
                        'total_points' => $totalPoints,
                        'notes' => $notes,
                        'consecutive_lateness' => $consecutiveLateness,
                        'recorded_by' => $user->id,
                        'recorded_at' => now()
                    ]
                );

                $savedAttendance[] = [
                    'student_id' => $studentId,
                    'status' => $status,
                    'attendance_points' => $attendancePoints,
                    'memorization_points' => $memorizationPoints,
                    'total_points' => $totalPoints,
                    'consecutive_lateness' => $consecutiveLateness,
                    'notes' => $notes
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الحضور بنجاح',
                'data' => [
                    'session_id' => $sessionId,
                    'attendance' => $savedAttendance,
                    'summary' => [
                        'total_students' => count($savedAttendance),
                        'present_count' => collect($savedAttendance)->where('status', 'present')->count(),
                        'absent_count' => collect($savedAttendance)->where('status', 'absent')->count(),
                        'late_count' => collect($savedAttendance)->where('status', 'late')->count(),
                        'excused_count' => collect($savedAttendance)->where('status', 'excused')->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الحضور'
            ], 500);
        }
    }

    /**
     * Get attendance for a session
     */
    public function getSessionAttendance(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            
            // Verify session exists and user has permission
            if ($user->role === 'teacher') {
                $teacher = Teacher::where('phone', $user->phone)->first();
                $session = Session::with(['circle.students', 'attendance'])
                    ->whereHas('circle', function($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })->find($sessionId);
            } else {
                // Admin can access all sessions
                $session = Session::with(['circle.students', 'attendance'])->find($sessionId);
            }

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة أو غير مصرح لك بالوصول إليها'
                ], 404);
            }

            // Get students with their attendance
            $students = $session->circle->students->map(function($student) use ($session) {
                $attendance = $session->attendance->where('student_id', $student->id)->first();
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'attendance' => $attendance ? [
                        'id' => $attendance->id,
                        'status' => $attendance->status,
                        'attendance_points' => $attendance->attendance_points,
                        'memorization_points' => $attendance->memorization_points,
                        'total_points' => $attendance->total_points,
                        'notes' => $attendance->notes,
                        'consecutive_lateness' => $attendance->consecutive_lateness,
                        'recorded_at' => $attendance->recorded_at
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                        'session_date' => $session->session_date,
                        'start_time' => $session->start_time,
                        'end_time' => $session->end_time,
                        'status' => $session->status,
                        'circle' => [
                            'id' => $session->circle->id,
                            'name' => $session->circle->name
                        ]
                    ],
                    'students' => $students,
                    'summary' => [
                        'total_students' => $students->count(),
                        'attendance_taken' => $session->attendance->count() > 0,
                        'present_count' => $session->attendance->where('status', 'present')->count(),
                        'absent_count' => $session->attendance->where('status', 'absent')->count(),
                        'late_count' => $session->attendance->where('status', 'late')->count(),
                        'excused_count' => $session->attendance->where('status', 'excused')->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الحضور'
            ], 500);
        }
    }

    /**
     * Get student attendance history
     */
    public function getStudentAttendanceHistory(Request $request, $studentId)
    {
        try {
            $user = $request->user();
            $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            // Verify student access
            $student = Student::find($studentId);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'الطالب غير موجود'
                ], 404);
            }

            // Check permissions
            if ($user->role === 'teacher') {
                $teacher = Teacher::where('phone', $user->phone)->first();
                if ($student->circle->teacher_id !== $teacher->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
                    ], 403);
                }
            } elseif ($user->role === 'parent') {
                if ($student->parent_phone !== $user->phone) {
                    return response()->json([
                        'success' => false,
                        'message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب'
                    ], 403);
                }
            }

            // Get attendance history
            $attendanceQuery = Attendance::with(['session.circle'])
                ->where('student_id', $studentId)
                ->whereHas('session', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('session_date', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'desc');

            $attendance = $attendanceQuery->paginate($perPage, ['*'], 'page', $page);

            // Calculate statistics
            $allAttendance = Attendance::where('student_id', $studentId)
                ->whereHas('session', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('session_date', [$startDate, $endDate]);
                })->get();

            $stats = [
                'total_sessions' => $allAttendance->count(),
                'present_count' => $allAttendance->where('status', 'present')->count(),
                'absent_count' => $allAttendance->where('status', 'absent')->count(),
                'late_count' => $allAttendance->where('status', 'late')->count(),
                'excused_count' => $allAttendance->where('status', 'excused')->count(),
                'total_points' => $allAttendance->sum('total_points'),
                'average_points' => $allAttendance->avg('total_points') ?? 0
            ];

            $stats['attendance_percentage'] = $stats['total_sessions'] > 0 
                ? round(($stats['present_count'] / $stats['total_sessions']) * 100, 1) 
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'circle' => [
                            'id' => $student->circle->id,
                            'name' => $student->circle->name
                        ]
                    ],
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ],
                    'statistics' => $stats,
                    'attendance' => $attendance->map(function($record) {
                        return [
                            'id' => $record->id,
                            'session' => [
                                'id' => $record->session->id,
                                'title' => $record->session->title,
                                'session_date' => $record->session->session_date,
                                'circle_name' => $record->session->circle->name
                            ],
                            'status' => $record->status,
                            'attendance_points' => $record->attendance_points,
                            'memorization_points' => $record->memorization_points,
                            'total_points' => $record->total_points,
                            'notes' => $record->notes,
                            'recorded_at' => $record->recorded_at
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $attendance->currentPage(),
                        'last_page' => $attendance->lastPage(),
                        'per_page' => $attendance->perPage(),
                        'total' => $attendance->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب سجل الحضور'
            ], 500);
        }
    }

    /**
     * Calculate attendance points based on status and consecutive lateness
     */
    private function calculateAttendancePoints($status, $studentId, $sessionDate)
    {
        switch ($status) {
            case 'present':
                return 5;
            case 'late':
                // Check consecutive lateness
                $consecutiveLateness = $this->getConsecutiveLateness($studentId, $sessionDate, 'late');
                return $consecutiveLateness > 1 ? 2 : 3;
            case 'excused':
                return 1;
            case 'absent':
            default:
                return 0;
        }
    }

    /**
     * Get consecutive lateness count
     */
    private function getConsecutiveLateness($studentId, $sessionDate, $currentStatus)
    {
        if ($currentStatus !== 'late') {
            return 0;
        }

        // Get recent attendance records for this student
        $recentAttendance = Attendance::whereHas('session', function($q) use ($sessionDate) {
            $q->where('session_date', '<', $sessionDate);
        })
        ->where('student_id', $studentId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->pluck('status')
        ->toArray();

        $consecutiveCount = 1; // Current late status
        
        foreach ($recentAttendance as $status) {
            if ($status === 'late') {
                $consecutiveCount++;
            } else {
                break;
            }
        }

        return $consecutiveCount;
    }
}

