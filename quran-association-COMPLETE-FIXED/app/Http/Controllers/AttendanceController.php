<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index()
    {
        $attendances = Attendance::with(['student', 'session', 'session.circle'])
            ->latest()
            ->paginate(20);
            
        $circles = Circle::with(['teacher', 'students'])->where('is_active', 1)->get();
        $today = today();
        
        // الجلسات المجدولة للمستقبل
        $upcomingSessions = ClassSession::with(['circle', 'circle.teacher'])
            ->where('session_date', '>=', $today)
            ->where('status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('actual_start_time')
            ->get();

        // الجلسات المكتملة في انتظار إكمال النقاط
        $pendingPointsSessions = ClassSession::with(['circle', 'circle.teacher', 'attendances'])
            ->where('status', 'attendance_taken') // الحالة الجديدة
            ->where('attendance_taken', true)
            ->orderBy('session_date', 'desc')
            ->orderBy('actual_start_time', 'desc')
            ->limit(20)
            ->get();

        // الجلسات المكتملة بالكامل (تم إكمال النقاط)
        $fullyCompletedSessions = ClassSession::with(['circle', 'circle.teacher', 'attendances'])
            ->where('status', 'completed') // مكتملة بالكامل
            ->where('attendance_taken', true)
            ->orderBy('session_date', 'desc')
            ->orderBy('actual_start_time', 'desc')
            ->limit(20)
            ->get();

        // الجلسات التي تحتاج تسجيل حضور (فائتة)
        $pendingSessions = ClassSession::with(['circle', 'circle.teacher'])
            ->where(function($query) {
                $query->where('status', 'missed')
                      ->orWhere(function($subQuery) {
                          $subQuery->where('status', 'scheduled')
                                   ->where('session_date', '<=', today());
                      });
            })
            ->where('attendance_taken', false)
            ->orderBy('session_date', 'desc')
            ->orderBy('actual_start_time', 'desc')
            ->limit(20)
            ->get();

        // الجلسات المؤرشفة السابقة (أكثر من 7 أيام وأقل من 3 أشهر)
        $archivedSessions = ClassSession::with(['circle', 'circle.teacher'])
            ->where('session_date', '>=', $today->copy()->subDays(90))
            ->where('session_date', '<', $today->copy()->subDays(7))
            ->where('attendance_taken', true)
            ->orderBy('session_date', 'desc')
            ->orderBy('actual_start_time', 'desc')
            ->limit(20)
            ->get();

        return view('attendance.index', compact(
            'attendances', 
            'circles', 
            'upcomingSessions', 
            'pendingPointsSessions', 
            'fullyCompletedSessions',
            'pendingSessions', 
            'archivedSessions',
            'today'
        ));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $circles = Circle::with('teacher')->where('status', 'active')->get();
        $sessions = ClassSession::with(['circle', 'circle.teacher'])
            ->where('session_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('actual_start_time')
            ->get();

        return view('attendance.create', compact('circles', 'sessions'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        // التحقق من عدم وجود سجل حضور مسبق
        $existingAttendance = Attendance::where('session_id', $request->session_id)
            ->where('student_id', $request->student_id)
            ->first();

        if ($existingAttendance) {
            return back()->withErrors(['error' => 'تم تسجيل حضور هذا الطالب مسبقاً لهذه الجلسة.']);
        }

        Attendance::create([
            'session_id' => $request->session_id,
            'student_id' => $request->student_id,
            'status' => $request->status,
            'notes' => $request->notes,
            'recorded_at' => now(),
        ]);

        return redirect()->route('attendance.index')
            ->with('success', 'تم تسجيل الحضور بنجاح.');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['student', 'session', 'session.circle', 'session.circle.teacher']);
        
        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $attendance->load(['student', 'session']);
        
        return view('attendance.edit', compact('attendance'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('attendance.index')
            ->with('success', 'تم تحديث سجل الحضور بنجاح.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'تم حذف سجل الحضور بنجاح.');
    }

    /**
     * Show attendance for a specific session.
     */
    public function showSession(ClassSession $session)
    {
        $session->load(['circle', 'circle.teacher', 'circle.students']);
        
        // تحديد نوع الجلسة بناءً على الوقت
        $sessionType = 'morning';
        if ($session->circle->start_time && strtotime($session->circle->start_time) >= strtotime('12:00:00')) {
            $sessionType = 'evening';
        }
        
        $attendances = Attendance::where('session_id', $session->id)
            ->with('student')
            ->get()
            ->keyBy('student_id');

        return view('attendance.session', compact('session', 'attendances', 'sessionType'));
    }

    /**
     * Store attendance for multiple students in a session.
     */
    public function storeSession(Request $request, ClassSession $session)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.memorization_points' => 'nullable|integer|min:0|max:5',
            'attendance.*.notes' => 'nullable|string|max:500',
            'action' => 'required|in:attendance_only,complete_with_points',
        ]);

        try {
            DB::transaction(function () use ($request, $session) {
                // حذف سجلات الحضور الموجودة لهذه الجلسة
                Attendance::where('session_id', $session->id)->delete();

                // تحديد حالة الجلسة بناءً على نوع الإجراء
                $sessionStatus = 'pending'; // افتراضي
                if ($request->action === 'attendance_only') {
                    $sessionStatus = 'attendance_taken'; // تم تسجيل الحضور، في انتظار النقاط
                } elseif ($request->action === 'complete_with_points') {
                    $sessionStatus = 'completed'; // مكتملة بالكامل
                }

                // إنشاء سجلات حضور جديدة
                foreach ($request->attendance as $studentId => $attendanceData) {
                    // حساب نقاط الحضور
                    $attendancePoints = 0;
                    switch ($attendanceData['status']) {
                        case 'present':
                            $attendancePoints = 5;
                            break;
                        case 'late':
                            // فحص التأخر المتتالي
                            $lastAttendance = Attendance::where('student_id', $studentId)
                                ->where('session_id', '!=', $session->id)
                                ->orderBy('created_at', 'desc')
                                ->first();
                            
                            $attendancePoints = ($lastAttendance && $lastAttendance->status === 'late') ? 2 : 3;
                            break;
                        case 'absent':
                        case 'excused':
                            $attendancePoints = 0;
                            break;
                    }

                    // نقاط الحفظ (فقط إذا كان الإجراء "حفظ وتسجيل النقاط")
                    $memorizationPoints = 0;
                    if ($request->action === 'complete_with_points') {
                        $memorizationPoints = (int) ($attendanceData['memorization_points'] ?? 0);
                    }
                    
                    // النقطة النهائية (الغائب = 0 حتى لو كان له نقاط حفظ)
                    $finalPoints = ($attendanceData['status'] === 'absent') ? 0 : ($attendancePoints + $memorizationPoints);

                    $attendanceRecord = [
                        'student_id' => $studentId,
                        'session_id' => $session->id,
                        'status' => $attendanceData['status'],
                        'points' => $attendancePoints,
                        'memorization_points' => $memorizationPoints,
                        'final_points' => $finalPoints,
                        'notes' => $attendanceData['notes'] ?? null,
                        'recorded_by' => auth()->id(),
                    ];
                    
                    Attendance::create($attendanceRecord);
                }

                // تحديث إحصائيات الجلسة
                $totalStudents = $session->circle->students->count();
                $presentStudents = count(array_filter($request->attendance, function($att) {
                    return $att['status'] === 'present';
                }));
                $absentStudents = $totalStudents - $presentStudents;
                $attendancePercentage = $totalStudents > 0 ? ($presentStudents / $totalStudents) * 100 : 0;

                $session->update([
                    'total_students' => $totalStudents,
                    'present_students' => $presentStudents,
                    'absent_students' => $absentStudents,
                    'attendance_percentage' => $attendancePercentage,
                    'attendance_taken' => true,
                    'attendance_taken_at' => now(),
                    'attendance_taken_by' => auth()->id(),
                    'status' => $sessionStatus, // حالة الجلسة حسب نوع الإجراء
                ]);
            });

            // رسالة النجاح حسب نوع الإجراء
            $successMessage = '';
            if ($request->action === 'attendance_only') {
                $successMessage = 'تم تسجيل الحضور بنجاح. الجلسة في انتظار تسجيل نقاط الحفظ.';
            } elseif ($request->action === 'complete_with_points') {
                $successMessage = 'تم تسجيل الحضور ونقاط الحفظ بنجاح. الجلسة مكتملة.';
            }

            return redirect()->route('attendance.session', $session->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('خطأ في حفظ الحضور: ' . $e->getMessage());
            \Log::error('تفاصيل الخطأ:', [
                'session_id' => $session->id,
                'error' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الحضور: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show attendance history for a specific student.
     */
    public function studentHistory(Student $student)
    {
        $attendances = Attendance::where('student_id', $student->id)
            ->with(['session', 'session.circle'])
            ->latest('recorded_at')
            ->paginate(20);

        $stats = [
            'total_sessions' => $attendances->total(),
            'present_count' => Attendance::where('student_id', $student->id)->where('status', 'present')->count(),
            'absent_count' => Attendance::where('student_id', $student->id)->where('status', 'absent')->count(),
            'late_count' => Attendance::where('student_id', $student->id)->where('status', 'late')->count(),
            'excused_count' => Attendance::where('student_id', $student->id)->where('status', 'excused')->count(),
        ];

        $stats['attendance_rate'] = $stats['total_sessions'] > 0 
            ? round(($stats['present_count'] + $stats['late_count']) / $stats['total_sessions'] * 100, 2)
            : 0;

        return view('attendance.student', compact('student', 'attendances', 'stats'));
    }
}

