<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherStudentController extends Controller
{
    /**
     * الحصول على قائمة الطلاب للمعلم
     */
    public function getMyStudents(Request $request)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $circles = $teacher->circles()->with(['students.guardians'])->get();
        $allStudents = collect();

        foreach ($circles as $circle) {
            foreach ($circle->students as $student) {
                $primaryGuardian = $student->primaryGuardian();
                
                $allStudents->push([
                    'id' => $student->id,
                    'name' => $student->name,
                    'age' => $student->age,
                    'gender' => $student->gender,
                    'education_level' => $student->education_level,
                    'is_active' => $student->is_active,
                    'circle' => [
                        'id' => $circle->id,
                        'name' => $circle->name,
                        'level' => $circle->level
                    ],
                    'primary_guardian' => $primaryGuardian ? [
                        'id' => $primaryGuardian->id,
                        'name' => $primaryGuardian->name,
                        'phone' => $primaryGuardian->phone,
                        'relationship' => $primaryGuardian->relationship_text
                    ] : null
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $allStudents,
                'total_count' => $allStudents->count(),
                'circles_count' => $circles->count()
            ]
        ]);
    }

    /**
     * الحصول على طلاب حلقة محددة
     */
    public function getCircleStudents(Request $request, $circleId)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $circle = $teacher->circles()->with(['students.guardians'])->find($circleId);

        if (!$circle) {
            return response()->json([
                'success' => false,
                'message' => 'الحلقة غير موجودة أو غير مرتبطة بحسابك'
            ], 404);
        }

        $studentsData = $circle->students->map(function ($student) use ($circle) {
            $primaryGuardian = $student->primaryGuardian();
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'age' => $student->age,
                'gender' => $student->gender,
                'education_level' => $student->education_level,
                'is_active' => $student->is_active,
                'primary_guardian' => $primaryGuardian ? [
                    'id' => $primaryGuardian->id,
                    'name' => $primaryGuardian->name,
                    'phone' => $primaryGuardian->phone,
                    'relationship' => $primaryGuardian->relationship_text
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'circle' => [
                    'id' => $circle->id,
                    'name' => $circle->name,
                    'level' => $circle->level,
                    'description' => $circle->description
                ],
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
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        // التحقق من أن الطالب في إحدى حلقات المعلم
        $student = Student::whereHas('circles', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with(['circles.teacher', 'guardians'])->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحلقاتك'
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
                        'description' => $currentCircle->description
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
     * تسجيل حضور الطالب
     */
    public function recordAttendance(Request $request, $studentId)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $validator = \Validator::make($request->all(), [
            'session_id' => 'required|exists:class_sessions,id',
            'status' => 'required|in:present,absent',
            'memorization_points' => 'nullable|numeric|min:0|max:10',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من أن الطالب في إحدى حلقات المعلم
        $student = Student::whereHas('circles', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحلقاتك'
            ], 404);
        }

        // التحقق من أن الجلسة تخص المعلم
        $session = ClassSession::whereHas('circle', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->find($request->session_id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'الجلسة غير موجودة أو غير مرتبطة بحلقاتك'
            ], 404);
        }

        try {
            // البحث عن سجل حضور موجود أو إنشاء جديد
            $attendance = Attendance::updateOrCreate([
                'student_id' => $studentId,
                'session_id' => $request->session_id
            ], [
                'status' => $request->status,
                'memorization_points' => $request->memorization_points,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح',
                'data' => [
                    'attendance' => [
                        'id' => $attendance->id,
                        'student_id' => $attendance->student_id,
                        'session_id' => $attendance->session_id,
                        'status' => $attendance->status,
                        'memorization_points' => $attendance->memorization_points,
                        'notes' => $attendance->notes,
                        'created_at' => $attendance->created_at->format('Y-m-d H:i')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الحضور'
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات الطالب
     */
    public function getStudentStatistics(Request $request, $studentId)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        // التحقق من أن الطالب في إحدى حلقات المعلم
        $student = Student::whereHas('circles', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحلقاتك'
            ], 404);
        }

        // حساب الإحصائيات للجلسات التي يدرسها المعلم فقط
        $totalSessions = ClassSession::whereHas('circle', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->whereHas('circle.students', function ($query) use ($studentId) {
            $query->where('students.id', $studentId);
        })->count();

        $attendedSessions = Attendance::where('student_id', $studentId)
            ->whereHas('session.circle', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'present')
            ->count();

        $absentSessions = Attendance::where('student_id', $studentId)
            ->whereHas('session.circle', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'absent')
            ->count();

        $totalPoints = Attendance::where('student_id', $studentId)
            ->whereHas('session.circle', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereNotNull('memorization_points')
            ->sum('memorization_points');

        $averagePoints = Attendance::where('student_id', $studentId)
            ->whereHas('session.circle', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereNotNull('memorization_points')
            ->avg('memorization_points');

        $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'absent_sessions' => $absentSessions,
                    'attendance_percentage' => $attendancePercentage,
                    'total_points' => $totalPoints,
                    'average_points' => round($averagePoints ?? 0, 1)
                ]
            ]
        ]);
    }

    /**
     * إرسال رسالة لولي الأمر
     */
    public function sendMessageToGuardian(Request $request, $studentId)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $validator = \Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'subject' => 'sometimes|string|max:255',
            'guardian_id' => 'sometimes|exists:guardians,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من أن الطالب في إحدى حلقات المعلم
        $student = Student::whereHas('circles', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with('guardians')->find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود أو غير مرتبط بحلقاتك'
            ], 404);
        }

        // تحديد ولي الأمر المستهدف
        if ($request->has('guardian_id')) {
            $guardian = $student->guardians()->find($request->guardian_id);
        } else {
            $guardian = $student->primaryGuardian();
        }

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد ولي أمر مرتبط بهذا الطالب'
            ], 404);
        }

        try {
            \App\Models\Notification::create([
                'title' => $request->get('subject', 'رسالة من المعلم'),
                'message' => $request->message,
                'type' => 'teacher_message',
                'sender_type' => 'teacher',
                'sender_id' => $teacher->id,
                'recipient_type' => 'guardian',
                'recipient_id' => $guardian->id,
                'data' => json_encode([
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'teacher_name' => $teacher->name,
                    'teacher_phone' => $teacher->phone
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

    /**
     * الحصول على حلقات المعلم
     */
    public function getMyCircles(Request $request)
    {
        $teacher = $request->user();
        
        if (!$teacher instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $circles = $teacher->circles()->withCount('students')->get();

        $circlesData = $circles->map(function ($circle) {
            return [
                'id' => $circle->id,
                'name' => $circle->name,
                'level' => $circle->level,
                'description' => $circle->description,
                'students_count' => $circle->students_count,
                'is_active' => $circle->is_active
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'circles' => $circlesData,
                'total_count' => $circlesData->count()
            ]
        ]);
    }
}

