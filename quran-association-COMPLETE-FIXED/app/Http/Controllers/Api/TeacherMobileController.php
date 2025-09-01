<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\Student;
use App\Models\Session;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherMobileController extends Controller
{
    /**
     * Get teacher dashboard data
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'المعلم غير موجود'
                ], 404);
            }

            // Get teacher's circles
            $circles = $teacher->circles()->with('students')->get();
            $totalStudents = $circles->sum(function($circle) {
                return $circle->students->count();
            });

            // Get today's sessions
            $today = Carbon::today();
            $todaySessions = Session::whereHas('circle', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->whereDate('session_date', $today)->count();

            // Calculate attendance percentage for this month
            $thisMonth = Carbon::now()->startOfMonth();
            $attendanceStats = DB::table('attendance')
                ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
                ->join('circles', 'class_sessions.circle_id', '=', 'circles.id')
                ->where('circles.teacher_id', $teacher->id)
                ->where('class_sessions.session_date', '>=', $thisMonth)
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count
                ')
                ->first();

            $attendancePercentage = $attendanceStats->total_records > 0 
                ? round(($attendanceStats->present_count / $attendanceStats->total_records) * 100, 1)
                : 0;

            // Get completed sessions this month
            $completedSessions = Session::whereHas('circle', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->where('session_date', '>=', $thisMonth)
              ->where('status', 'completed')
              ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => [
                        'id' => $teacher->id,
                        'name' => $teacher->name,
                        'phone' => $teacher->phone,
                        'email' => $teacher->email
                    ],
                    'stats' => [
                        'total_circles' => $circles->count(),
                        'total_students' => $totalStudents,
                        'today_sessions' => $todaySessions,
                        'attendance_percentage' => $attendancePercentage,
                        'completed_sessions' => $completedSessions
                    ],
                    'circles' => $circles->map(function($circle) {
                        return [
                            'id' => $circle->id,
                            'name' => $circle->name,
                            'students_count' => $circle->students->count(),
                            'schedule' => $circle->schedule
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات لوحة التحكم'
            ], 500);
        }
    }

    /**
     * Get teacher's sessions
     */
    public function sessions(Request $request)
    {
        try {
            $user = $request->user();
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'المعلم غير موجود'
                ], 404);
            }

            $filter = $request->get('filter', 'all'); // all, today, upcoming, completed
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            $query = Session::whereHas('circle', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->with(['circle', 'attendance']);

            switch ($filter) {
                case 'today':
                    $query->whereDate('session_date', Carbon::today());
                    break;
                case 'upcoming':
                    $query->where('session_date', '>', Carbon::now());
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
            }

            $sessions = $query->orderBy('session_date', 'desc')
                            ->orderBy('actual_start_time', 'desc')
                            ->paginate($perPage, ['*'], 'page', $page);

            $sessionsData = $sessions->map(function($session) {
                $attendanceCount = $session->attendance->count();
                $presentCount = $session->attendance->where('status', 'present')->count();
                $attendancePercentage = $attendanceCount > 0 ? round(($presentCount / $attendanceCount) * 100, 1) : 0;

                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'circle' => [
                        'id' => $session->circle->id,
                        'name' => $session->circle->name
                    ],
                    'session_date' => $session->session_date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'actual_start_time' => $session->actual_start_time,
                    'actual_end_time' => $session->actual_end_time,
                    'status' => $session->status,
                    'notes' => $session->notes,
                    'attendance_stats' => [
                        'total_students' => $attendanceCount,
                        'present_count' => $presentCount,
                        'absent_count' => $session->attendance->where('status', 'absent')->count(),
                        'late_count' => $session->attendance->where('status', 'late')->count(),
                        'attendance_percentage' => $attendancePercentage,
                        'attendance_taken' => $attendanceCount > 0
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessionsData,
                    'pagination' => [
                        'current_page' => $sessions->currentPage(),
                        'last_page' => $sessions->lastPage(),
                        'per_page' => $sessions->perPage(),
                        'total' => $sessions->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الجلسات'
            ], 500);
        }
    }

    /**
     * Get session details with students for attendance
     */
    public function sessionDetails(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'المعلم غير موجود'
                ], 404);
            }

            $session = Session::with(['circle.students', 'attendance'])
                ->whereHas('circle', function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->find($sessionId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }

            // Get students with their attendance for this session
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
                        'consecutive_lateness' => $attendance->consecutive_lateness
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                        'circle' => [
                            'id' => $session->circle->id,
                            'name' => $session->circle->name
                        ],
                        'session_date' => $session->session_date,
                        'start_time' => $session->start_time,
                        'end_time' => $session->end_time,
                        'actual_start_time' => $session->actual_start_time,
                        'actual_end_time' => $session->actual_end_time,
                        'status' => $session->status,
                        'notes' => $session->notes
                    ],
                    'students' => $students
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الجلسة'
            ], 500);
        }
    }

    /**
     * Create new session
     */
    public function createSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'circle_id' => 'required|exists:circles,id',
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string'
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
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            // Verify circle belongs to teacher
            $circle = Circle::where('id', $request->circle_id)
                          ->where('teacher_id', $teacher->id)
                          ->first();
            
            if (!$circle) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحلقة غير موجودة أو غير مخصصة لك'
                ], 403);
            }

            $session = Session::create([
                'circle_id' => $request->circle_id,
                'title' => $request->title,
                'session_date' => $request->session_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'scheduled',
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الجلسة بنجاح',
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                        'circle' => [
                            'id' => $circle->id,
                            'name' => $circle->name
                        ],
                        'session_date' => $session->session_date,
                        'start_time' => $session->start_time,
                        'end_time' => $session->end_time,
                        'status' => $session->status,
                        'notes' => $session->notes
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسة'
            ], 500);
        }
    }

    /**
     * Start session
     */
    public function startSession(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            $session = Session::whereHas('circle', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->find($sessionId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }

            if ($session->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن بدء هذه الجلسة'
                ], 400);
            }

            $session->update([
                'status' => 'ongoing',
                'actual_start_time' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم بدء الجلسة بنجاح',
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'status' => $session->status,
                        'actual_start_time' => $session->actual_start_time
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء بدء الجلسة'
            ], 500);
        }
    }

    /**
     * End session
     */
    public function endSession(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $teacher = Teacher::where('phone', $user->phone)->first();
            
            $session = Session::whereHas('circle', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->find($sessionId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }

            if ($session->status !== 'ongoing') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إنهاء هذه الجلسة'
                ], 400);
            }

            $session->update([
                'status' => 'completed',
                'actual_end_time' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنهاء الجلسة بنجاح',
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'status' => $session->status,
                        'actual_end_time' => $session->actual_end_time
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنهاء الجلسة'
            ], 500);
        }
    }
}

