<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuardianStudentController extends Controller
{
    /**
     * الحصول على قائمة الطلاب لولي الأمر
     */
    public function getMyStudents(Request $request)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $students = $guardian->students()->with(['circles.teacher'])->get();

        $studentsData = $students->map(function ($student) {
            $currentCircle = $student->current_circle;
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'age' => $student->age,
                'gender' => $student->gender,
                'education_level' => $student->education_level,
                'is_active' => $student->is_active,
                'circle' => $currentCircle ? [
                    'id' => $currentCircle->id,
                    'name' => $currentCircle->name,
                    'level' => $currentCircle->level,
                    'teacher' => $currentCircle->teacher ? [
                        'id' => $currentCircle->teacher->id,
                        'name' => $currentCircle->teacher->name,
                        'phone' => $currentCircle->teacher->phone
                    ] : null
                ] : null,
                'relationship' => $student->pivot->relationship_type ?? 'غير محدد',
                'is_primary' => $student->pivot->is_primary ?? false
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $studentsData,
                'total_count' => $studentsData->count()
            ]
        ]);
    }

    /**
     * الحصول على تفاصيل طالب محدد
     */
    public function getStudentDetails(Request $request, $studentId)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $student = $guardian->students()->with(['circles.teacher', 'guardians'])->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحسابك'
            ], 404);
        }

        $currentCircle = $student->current_circle;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'age' => $student->age,
                    'gender' => $student->gender,
                    'birth_date' => $student->birth_date?->format('Y-m-d'),
                    'education_level' => $student->education_level,
                    'address' => $student->address,
                    'notes' => $student->notes,
                    'is_active' => $student->is_active,
                    'circle' => $currentCircle ? [
                        'id' => $currentCircle->id,
                        'name' => $currentCircle->name,
                        'level' => $currentCircle->level,
                        'description' => $currentCircle->description,
                        'teacher' => $currentCircle->teacher ? [
                            'id' => $currentCircle->teacher->id,
                            'name' => $currentCircle->teacher->name,
                            'phone' => $currentCircle->teacher->phone,
                            'email' => $currentCircle->teacher->email
                        ] : null
                    ] : null,
                    'guardians' => $student->guardians->map(function ($guardian) {
                        return [
                            'id' => $guardian->id,
                            'name' => $guardian->name,
                            'phone' => $guardian->phone,
                            'email' => $guardian->email,
                            'relationship' => $guardian->pivot->relationship_type ?? 'غير محدد',
                            'is_primary' => $guardian->pivot->is_primary ?? false
                        ];
                    })
                ]
            ]
        ]);
    }

    /**
     * الحصول على إحصائيات الطالب
     */
    public function getStudentStatistics(Request $request, $studentId)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $student = $guardian->students()->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحسابك'
            ], 404);
        }

        // حساب الإحصائيات
        $totalSessions = ClassSession::whereHas('circle.students', function ($query) use ($studentId) {
            $query->where('students.id', $studentId);
        })->count();

        $attendedSessions = Attendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();

        $absentSessions = Attendance::where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();

        $totalPoints = Attendance::where('student_id', $studentId)
            ->whereNotNull('memorization_points')
            ->sum('memorization_points');

        $averagePoints = Attendance::where('student_id', $studentId)
            ->whereNotNull('memorization_points')
            ->avg('memorization_points');

        $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0;

        // آخر 10 جلسات
        $recentSessions = Attendance::where('student_id', $studentId)
            ->with(['session.circle'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'date' => $attendance->session->date ?? $attendance->created_at->format('Y-m-d'),
                    'status' => $attendance->status,
                    'status_text' => $attendance->status === 'present' ? 'حاضر' : 'غائب',
                    'memorization_points' => $attendance->memorization_points,
                    'notes' => $attendance->notes,
                    'circle_name' => $attendance->session->circle->name ?? 'غير محدد'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'absent_sessions' => $absentSessions,
                    'attendance_percentage' => $attendancePercentage,
                    'total_points' => $totalPoints,
                    'average_points' => round($averagePoints ?? 0, 1),
                    'recent_sessions' => $recentSessions
                ]
            ]
        ]);
    }

    /**
     * الحصول على سجل الحضور للطالب
     */
    public function getStudentAttendance(Request $request, $studentId)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $student = $guardian->students()->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحسابك'
            ], 404);
        }

        // فلترة حسب التاريخ إذا تم تمريرها
        $query = Attendance::where('student_id', $studentId)
            ->with(['session.circle']);

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // فلترة حسب الشهر الحالي إذا لم يتم تحديد تواريخ
        if (!$request->has('from_date') && !$request->has('to_date')) {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $attendanceRecords = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        $records = $attendanceRecords->getCollection()->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'date' => $attendance->session->date ?? $attendance->created_at->format('Y-m-d'),
                'status' => $attendance->status,
                'status_text' => $attendance->status === 'present' ? 'حاضر' : 'غائب',
                'memorization_points' => $attendance->memorization_points,
                'notes' => $attendance->notes,
                'circle' => [
                    'id' => $attendance->session->circle->id ?? null,
                    'name' => $attendance->session->circle->name ?? 'غير محدد'
                ],
                'created_at' => $attendance->created_at->format('Y-m-d H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'attendance_records' => $records,
                'pagination' => [
                    'current_page' => $attendanceRecords->currentPage(),
                    'last_page' => $attendanceRecords->lastPage(),
                    'per_page' => $attendanceRecords->perPage(),
                    'total' => $attendanceRecords->total()
                ]
            ]
        ]);
    }

    /**
     * الحصول على معلومات المعلم للطالب
     */
    public function getStudentTeacher(Request $request, $studentId)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $student = $guardian->students()->with(['circles.teacher'])->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحسابك'
            ], 404);
        }

        $currentCircle = $student->current_circle;
        $teacher = $currentCircle?->teacher;

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد معلم مرتبط بهذا الطالب حالياً'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'specialization' => $teacher->specialization,
                    'qualification' => $teacher->qualification,
                    'experience' => $teacher->experience
                ],
                'circle' => [
                    'id' => $currentCircle->id,
                    'name' => $currentCircle->name,
                    'level' => $currentCircle->level,
                    'description' => $currentCircle->description
                ]
            ]
        ]);
    }

    /**
     * إرسال رسالة للمعلم
     */
    public function sendMessageToTeacher(Request $request, $studentId)
    {
        $guardian = $request->user();
        
        if (!$guardian instanceof Guardian) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $student = $guardian->students()->with(['circles.teacher'])->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحسابك'
            ], 404);
        }

        $validator = \Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'subject' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $currentCircle = $student->current_circle;
        $teacher = $currentCircle?->teacher;

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد معلم مرتبط بهذا الطالب حالياً'
            ], 404);
        }

        // هنا يمكن إضافة منطق إرسال الرسالة (إشعارات، بريد إلكتروني، إلخ)
        // للتبسيط، سنقوم بحفظ الرسالة في جدول الإشعارات

        try {
            \App\Models\Notification::create([
                'title' => $request->get('subject', 'رسالة من ولي الأمر'),
                'message' => $request->message,
                'type' => 'guardian_message',
                'sender_type' => 'guardian',
                'sender_id' => $guardian->id,
                'recipient_type' => 'teacher',
                'recipient_id' => $teacher->id,
                'data' => json_encode([
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'guardian_name' => $guardian->name,
                    'guardian_phone' => $guardian->phone
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }
}

