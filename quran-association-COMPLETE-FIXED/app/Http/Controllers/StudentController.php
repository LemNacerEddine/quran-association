<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Circle;
use App\Models\Attendance;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::with(['circle', 'circles', 'attendances' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->paginate(20);

        // إضافة إحصائيات صحيحة لكل طالب
        $students->getCollection()->transform(function ($student) {
            // إحصائيات الحضور الصحيحة
            $attendanceStats = \DB::table('attendance')
                ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
                ->where('attendance.student_id', $student->id)
                ->selectRaw('
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                    ROUND(
                        CASE 
                            WHEN COUNT(*) > 0 THEN 
                                (SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                            ELSE 0 
                        END, 1
                    ) as attendance_percentage,
                    SUM(COALESCE(attendance.final_points, 0)) as total_points,
                    ROUND(AVG(COALESCE(attendance.final_points, 0)), 1) as avg_points
                ')
                ->first();

            // إحصائيات النقاط من memorization_points
            $memorizationStats = \DB::table('memorization_points')
                ->where('student_id', $student->id)
                ->selectRaw('
                    SUM(points) as total_memorization_points,
                    COUNT(*) as memorization_sessions,
                    ROUND(AVG(points), 1) as avg_memorization_points
                ')
                ->first();

            // تجميع الإحصائيات
            $student->summary = (object)[
                'total_sessions' => $attendanceStats->total_sessions ?? 0,
                'present_count' => $attendanceStats->present_count ?? 0,
                'absent_count' => $attendanceStats->absent_count ?? 0,
                'late_count' => $attendanceStats->late_count ?? 0,
                'attendance_percentage' => $attendanceStats->attendance_percentage ?? 0,
                'total_points' => ($attendanceStats->total_points ?? 0) + ($memorizationStats->total_memorization_points ?? 0),
                'avg_points' => $attendanceStats->avg_points ?? 0,
                'memorization_points' => $memorizationStats->total_memorization_points ?? 0,
                'memorization_sessions' => $memorizationStats->memorization_sessions ?? 0,
            ];

            return $student;
        });

        // إحصائيات عامة محدثة
        $generalStats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('is_active', true)->count(),
            'avg_attendance_rate' => $students->getCollection()->avg('summary.attendance_percentage') ?? 0,
            'total_sessions' => \DB::table('class_sessions')->count(),
            'completed_sessions' => \DB::table('class_sessions')->where('status', 'completed')->count(),
            'missed_sessions' => \DB::table('class_sessions')->where('status', 'missed')->count(),
        ];

        // أفضل الطلاب (حسب النقاط)
        $topStudents = $students->getCollection()
            ->sortByDesc('summary.total_points')
            ->take(3);

        // الطلاب المحتاجون لمتابعة (حضور أقل من 70%)
        $studentsNeedingAttention = $students->getCollection()
            ->filter(function($student) {
                return $student->summary->attendance_percentage < 70;
            })
            ->take(5);

        return view('students.index', compact('students', 'generalStats', 'topStudents', 'studentsNeedingAttention'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $circles = Circle::active()->get();
        
        return view('students.create', compact('circles'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email',
            'medical_notes' => 'nullable|string',
            'level' => 'required|string|max:100',
            'status' => 'required|in:active,inactive',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string',
            'circles' => 'nullable|array',
            'circles.*' => 'exists:circles,id',
        ]);

        $student = Student::create($request->except('circles'));

        // ربط الطالب بالحلقات المحددة
        if ($request->has('circles')) {
            $circleData = [];
            foreach ($request->circles as $circleId) {
                $circleData[$circleId] = [
                    'enrollment_date' => $request->enrollment_date,
                    'status' => 'active',
                ];
            }
            $student->circles()->attach($circleData);
        }

        return redirect()->route('students.index')
            ->with('success', 'تم تسجيل الطالب بنجاح.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        // تحميل الـ relationships بشكل آمن
        try {
            $student->load(['circles', 'attendances.session', 'guardian']);
        } catch (\Exception $e) {
            // في حالة وجود مشكلة في الـ relationships، نتجاهلها مؤقتاً
        }
        
        // نفس الاستعلام المستخدم في واجهة ولي الأمر (نسخة مطابقة)
        $attendanceStats = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('attendance.student_id', $student->id)
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                ROUND(
                    CASE 
                        WHEN COUNT(*) > 0 THEN 
                            (SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                        ELSE 0 
                    END, 1
                ) as attendance_percentage,
                SUM(COALESCE(attendance.final_points, 0)) as total_points,
                ROUND(AVG(COALESCE(attendance.final_points, 0)), 1) as avg_points
            ')
            ->first();

        // آخر 20 جلسة حضور (نفس الطريقة مع أسماء الأعمدة الصحيحة)
        $recentAttendances = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'class_sessions.circle_id', '=', 'circles.id')
            ->where('attendance.student_id', $student->id)
            ->select([
                'attendance.*',
                'class_sessions.session_date',
                'class_sessions.actual_start_time',
                'class_sessions.actual_end_time',
                'circles.name as circle_name'
            ])
            ->orderBy('class_sessions.session_date', 'desc')
            ->limit(20)
            ->get();

        // تجميع الإحصائيات (نفس طريقة واجهة ولي الأمر)
        $student->summary = (object)[
            'total_sessions' => $attendanceStats->total_sessions ?? 0,
            'present_count' => $attendanceStats->present_count ?? 0,
            'absent_count' => $attendanceStats->absent_count ?? 0,
            'late_count' => $attendanceStats->late_count ?? 0,
            'attendance_percentage' => $attendanceStats->attendance_percentage ?? 0,
            'total_points' => $attendanceStats->total_points ?? 0,
            'avg_points' => $attendanceStats->avg_points ?? 0,
        ];
        
        return view('students.show', compact('student', 'recentAttendances'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $circles = Circle::active()->get();
        $studentCircles = $student->circles->pluck('id')->toArray();
        
        return view('students.edit', compact('student', 'circles', 'studentCircles'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'is_active' => 'nullable|boolean',
        ]);

        // تحديث البيانات الأساسية
        $student->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        // التحقق من وجود سجلات حضور
        if ($student->attendances()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف الطالب لوجود سجلات حضور مرتبطة به.']);
        }

        // إلغاء ربط الطالب بالحلقات
        $student->circles()->detach();
        
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'تم حذف الطالب بنجاح.');
    }
}

