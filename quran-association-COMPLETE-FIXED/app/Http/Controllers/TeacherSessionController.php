<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Circle;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session as SessionFacade;
use Carbon\Carbon;

class TeacherSessionController extends Controller
{
    public function index()
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        $teacher = Teacher::with(['circles'])->find($teacherId);
        $circles = $teacher->circles;

        // Get all sessions for teacher's circles
        $sessions = Session::whereIn('circle_id', $circles->pluck('id'))
                          ->with(['circle', 'attendances.student'])
                          ->orderBy('session_date', 'desc')
                          ->orderBy('actual_start_time', 'desc')
                          ->paginate(10);

        return view('teacher-auth.sessions.index', compact('sessions', 'teacher', 'circles'));
    }

    public function create()
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        $teacher = Teacher::with(['circles'])->find($teacherId);
        $circles = $teacher->circles;

        return view('teacher-auth.sessions.create', compact('teacher', 'circles'));
    }

    public function store(Request $request)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        $request->validate([
            'circle_id' => 'required|exists:circles,id',
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string'
        ]);

        // Verify teacher owns this circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $request->circle_id)->exists()) {
            return back()->withErrors(['circle_id' => 'غير مسموح لك بإنشاء جلسة لهذه الحلقة']);
        }

        $session = Session::create([
            'circle_id' => $request->circle_id,
            'teacher_id' => $teacherId,
            'session_title' => $request->title,
            'session_description' => $request->description,
            'session_date' => $request->session_date,
            'actual_start_time' => $request->start_time,
            'actual_end_time' => $request->end_time,
            'status' => 'scheduled'
        ]);

        return redirect()->route('teacher.sessions.show', $session)
                        ->with('success', 'تم إنشاء الجلسة بنجاح');
    }

    public function show(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بعرض هذه الجلسة');
        }

        $session->load(['circle.students', 'attendances.student']);
        
        // Get students who haven't been marked for attendance yet
        $studentsWithoutAttendance = $session->circle->students()
            ->whereNotIn('students.id', $session->attendances->pluck('student_id'))
            ->get();

        return view('teacher-auth.sessions.show', compact('session', 'teacher', 'studentsWithoutAttendance'));
    }

    public function start(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك ببدء هذه الجلسة');
        }

        if ($session->status !== 'scheduled') {
            return back()->withErrors(['session' => 'لا يمكن بدء هذه الجلسة']);
        }

        $session->update([
            'status' => 'ongoing'
        ]);

        // Redirect to the live session page instead of back to show
        return redirect()->route('teacher.sessions.live', $session)
                        ->with('success', 'تم بدء الجلسة بنجاح');
    }

    public function end(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بإنهاء هذه الجلسة');
        }

        if ($session->status !== 'ongoing') {
            return back()->withErrors(['session' => 'لا يمكن إنهاء هذه الجلسة']);
        }

        $session->update([
            'status' => 'completed'
        ]);

        return redirect()->route('teacher.sessions.show', $session)
                        ->with('success', 'تم إنهاء الجلسة بنجاح');
    }

    public function attendance(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بإدارة حضور هذه الجلسة');
        }

        $session->load(['circle.students', 'attendances.student']);
        
        return view('teacher-auth.sessions.attendance', compact('session', 'teacher'));
    }

    public function storeAttendance(Request $request, Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بتسجيل حضور هذه الجلسة');
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,excused'
        ]);

        foreach ($request->attendance as $studentId => $status) {
            // Verify student belongs to this circle
            if (!$session->circle->students()->where('students.id', $studentId)->exists()) {
                continue;
            }

            // Get memorization points if provided
            $memorizationPoints = $request->input("memorization_points.{$studentId}", 0);
            $notes = $request->input("notes.{$studentId}", '');

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'session_id' => $session->id,
                    'student_id' => $studentId
                ],
                [
                    'status' => $status,
                    'notes' => $notes,
                    'marked_at' => now(),
                    'recorded_by' => $teacherId
                ]
            );

            // Update points using the new system
            $attendance->updatePoints($memorizationPoints);
        }

        return redirect()->route('teacher.sessions.attendance', $session)
                        ->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function edit(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بتعديل هذه الجلسة');
        }

        $circles = $teacher->circles;

        return view('teacher-auth.sessions.edit', compact('session', 'teacher', 'circles'));
    }

    public function update(Request $request, Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بتعديل هذه الجلسة');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string'
        ]);

        $session->update([
            'title' => $request->title,
            'description' => $request->description,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        return redirect()->route('teacher.sessions.show', $session)
                        ->with('success', 'تم تحديث الجلسة بنجاح');
    }

    public function destroy(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بحذف هذه الجلسة');
        }

        if ($session->status === 'completed') {
            return back()->withErrors(['session' => 'لا يمكن حذف جلسة مكتملة']);
        }

        $session->delete();

        return redirect()->route('teacher.sessions.index')
                        ->with('success', 'تم حذف الجلسة بنجاح');
    }

    public function live(Session $session)
    {
        $teacherId = SessionFacade::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        // Verify teacher owns this session's circle
        $teacher = Teacher::find($teacherId);
        if (!$teacher->circles()->where('circles.id', $session->circle_id)->exists()) {
            abort(403, 'غير مسموح لك بالوصول لهذه الجلسة');
        }

        if ($session->status !== 'in_progress') {
            return redirect()->route('teacher.sessions.show', $session)
                           ->withErrors(['session' => 'هذه الجلسة غير نشطة حالياً']);
        }

        // Load session with related data
        $session->load(['circle.students', 'attendances.student']);
        
        // Get students with their attendance status
        $students = $session->circle->students->map(function ($student) use ($session) {
            $attendance = $session->attendances->where('student_id', $student->id)->first();
            $student->attendance_status = $attendance ? $attendance->status : 'not_marked';
            $student->attendance_points = $attendance ? $attendance->points : 0;
            $student->attendance_notes = $attendance ? $attendance->notes : '';
            return $student;
        });

        // Calculate session statistics
        $stats = [
            'total_students' => $students->count(),
            'present' => $students->where('attendance_status', 'present')->count(),
            'absent' => $students->where('attendance_status', 'absent')->count(),
            'late' => $students->where('attendance_status', 'late')->count(),
            'excused' => $students->where('attendance_status', 'excused')->count(),
            'not_marked' => $students->where('attendance_status', 'not_marked')->count(),
            'total_points' => $students->sum('attendance_points'),
            'session_duration' => $session->actual_start_time ? 
                now()->diffInMinutes($session->actual_start_time) : 0
        ];

        return view('teacher-auth.sessions.live', compact('session', 'teacher', 'students', 'stats'));
    }
}

