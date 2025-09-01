<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\AttendanceSession;
use App\Models\AbsenceReason;
use App\Models\Circle;
use App\Models\ClassSchedule;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassSession::with(['circle.teacher', 'schedule']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('session_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('session_date', '<=', $request->date_to);
        }

        if ($request->filled('circle_id')) {
            $query->where('circle_id', $request->circle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('attendance_taken')) {
            if ($request->attendance_taken === 'yes') {
                $query->where('attendance_taken', true);
            } else {
                $query->where('attendance_taken', false);
            }
        }

        // Get all sessions for classification
        $allSessions = $query->orderBy('session_date', 'desc')
                            ->orderBy('actual_start_time', 'desc')
                            ->get();

        // Classify sessions by time and status
        $classifiedSessions = $this->classifySessions($allSessions);

        // Determine which section to show (default: today)
        $activeSection = $request->get('section', 'today');
        
        // Get sessions for the active section
        $sectionSessions = $this->getSectionSessions($classifiedSessions, $activeSection);

        // Calculate stats
        $stats = [
            'total_sessions' => ClassSession::count(),
            'today_sessions' => count($classifiedSessions['today']),
            'upcoming_sessions' => count($classifiedSessions['upcoming']),
            'missed_sessions' => count($classifiedSessions['missed']),
            'completed_sessions' => count($classifiedSessions['completed']),
            'attendance_rate' => $this->calculateAverageAttendance()
        ];

        $circles = Circle::with('teacher')->get();

        return view('sessions.index_compact', compact('sectionSessions', 'stats', 'circles', 'classifiedSessions', 'activeSection'));
    }

    public function create()
    {
        $circles = Circle::with('teacher')->get();
        $schedules = ClassSchedule::with('circle')->where('is_active', true)->get();
        
        return view('sessions.create', compact('circles', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_title' => 'required|string|max:255',
            'circle_id' => 'required|exists:circles,id',
            'schedule_id' => 'nullable|exists:class_schedules,id',
            'session_date' => 'required|date',
            'session_description' => 'nullable|string',
            'lesson_content' => 'nullable|string',
            'homework' => 'nullable|string'
        ]);

        $circle = Circle::find($validated['circle_id']);
        $validated['teacher_id'] = $circle->teacher_id;

        $session = ClassSession::create($validated);

        return redirect()->route('sessions.index')
                        ->with('success', 'تم إنشاء الجلسة بنجاح.');
    }

    public function show(ClassSession $session)
    {
        $session->load([
            'circle.teacher', 
            'circle.students', 
            'schedule',
            'attendanceSessions.student'
        ]);

        $attendanceStats = $this->getSessionAttendanceStats($session);

        return view('sessions.show', compact('session', 'attendanceStats'));
    }

    public function edit(ClassSession $session)
    {
        $circles = Circle::with('teacher')->get();
        $schedules = ClassSchedule::with('circle')->where('is_active', true)->get();
        
        return view('sessions.edit', compact('session', 'circles', 'schedules'));
    }

    public function update(Request $request, ClassSession $session)
    {
        $validated = $request->validate([
            'session_title' => 'required|string|max:255',
            'circle_id' => 'required|exists:circles,id',
            'schedule_id' => 'nullable|exists:class_schedules,id',
            'session_date' => 'required|date',
            'actual_start_time' => 'nullable|date_format:H:i',
            'actual_end_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'attendance_taken' => 'boolean',
            'session_description' => 'nullable|string',
            'lesson_content' => 'nullable|string',
            'homework' => 'nullable|string',
            'session_notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string'
        ]);

        $circle = Circle::find($validated['circle_id']);
        $validated['teacher_id'] = $circle->teacher_id;

        $session->update($validated);

        return redirect()->route('sessions.show', $session)
                        ->with('success', 'تم تحديث الجلسة بنجاح.');
    }

    public function destroy(ClassSession $session)
    {
        // Don't allow deletion of completed sessions with attendance
        if ($session->status === 'completed' && $session->attendance_taken) {
            return back()->withErrors(['cannot_delete' => 'لا يمكن حذف جلسة مكتملة تم تسجيل الحضور فيها.']);
        }

        $session->delete();

        return redirect()->route('sessions.index')
                        ->with('success', 'تم حذف الجلسة بنجاح.');
    }

    public function start(ClassSession $session)
    {
        if ($session->status !== 'scheduled') {
            return back()->withErrors(['invalid_status' => 'لا يمكن بدء الجلسة في الحالة الحالية.']);
        }

        $session->update([
            'status' => 'ongoing',
            'actual_start_time' => now()->format('H:i')
        ]);

        return back()->with('success', 'تم بدء الجلسة بنجاح.');
    }

    public function complete(ClassSession $session)
    {
        if (!in_array($session->status, ['scheduled', 'ongoing'])) {
            return back()->withErrors(['invalid_status' => 'لا يمكن إنهاء الجلسة في الحالة الحالية.']);
        }

        $session->update([
            'status' => 'completed',
            'actual_end_time' => now()->format('H:i')
        ]);

        // Send notifications to parents if attendance was taken
        if ($session->attendance_taken) {
            $this->sendAttendanceNotifications($session);
        }

        return back()->with('success', 'تم إنهاء الجلسة بنجاح.');
    }

    public function attendance(ClassSession $session)
    {
        $session->load(['circle.students', 'attendanceSessions']);
        $absenceReasons = AbsenceReason::active()->ordered()->get();

        return view('sessions.attendance', compact('session', 'absenceReasons'));
    }

    public function updateAttendance(Request $request, ClassSession $session)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.arrival_time' => 'nullable|date_format:H:i',
            'attendances.*.absence_reason_id' => 'nullable|exists:absence_reasons,id',
            'attendances.*.absence_reason' => 'nullable|string',
            'attendances.*.participation_score' => 'nullable|numeric|min:0|max:10',
            'attendances.*.notes' => 'nullable|string',
            'attendances.*.behavior_notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($session, $validated) {
            // Delete existing attendance records
            $session->attendanceSessions()->delete();

            // Create new attendance records
            foreach ($validated['attendances'] as $attendance) {
                AttendanceSession::create([
                    'session_id' => $session->id,
                    'student_id' => $attendance['student_id'],
                    'circle_id' => $session->circle_id,
                    'status' => $attendance['status'],
                    'arrival_time' => $attendance['arrival_time'] ?? null,
                    'absence_reason' => $attendance['absence_reason'] ?? null,
                    'notes' => $attendance['notes'] ?? null,
                    'behavior_notes' => $attendance['behavior_notes'] ?? null,
                    'participation_score' => $attendance['participation_score'] ?? null,
                    'recorded_by' => auth()->id(),
                    'recorded_at' => now()
                ]);
            }

            // Update session attendance status
            $session->update([
                'attendance_taken' => true,
                'attendance_taken_at' => now(),
                'attendance_taken_by' => auth()->id()
            ]);

            // Update session stats
            $this->updateSessionStats($session);
        });

        // Send notifications to parents
        $this->sendAttendanceNotifications($session);

        return redirect()->route('sessions.index')
                        ->with('success', 'تم حفظ بيانات الحضور بنجاح.');
    }

    public function quickAttendance(Request $request)
    {
        $validated = $request->validate([
            'circle_id' => 'required|exists:circles,id',
            'session_date' => 'required|date'
        ]);

        $circle = Circle::find($validated['circle_id']);
        $sessionDate = Carbon::parse($validated['session_date']);

        // Look for existing session
        $session = ClassSession::where('circle_id', $validated['circle_id'])
                              ->whereDate('session_date', $sessionDate)
                              ->first();

        // If no session exists, create one
        if (!$session) {
            // Try to find a schedule for this day
            $dayOfWeek = strtolower($sessionDate->format('l'));
            $schedule = ClassSchedule::where('circle_id', $validated['circle_id'])
                                   ->where('day_of_week', $dayOfWeek)
                                   ->where('is_active', true)
                                   ->first();

            $session = ClassSession::create([
                'schedule_id' => $schedule?->id,
                'circle_id' => $validated['circle_id'],
                'teacher_id' => $circle->teacher_id,
                'session_title' => $circle->name . ' - ' . $sessionDate->format('Y-m-d'),
                'session_date' => $sessionDate,
                'status' => 'ongoing'
            ]);
        }

        return redirect()->route('sessions.attendance', $session);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:complete,cancel,delete',
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:class_sessions,id',
            'cancellation_reason' => 'nullable|string'
        ]);

        $sessionIds = json_decode($validated['session_ids']);
        $sessions = ClassSession::whereIn('id', $sessionIds);

        switch ($validated['action']) {
            case 'complete':
                $sessions->whereIn('status', ['scheduled', 'ongoing'])
                        ->update([
                            'status' => 'completed',
                            'actual_end_time' => now()->format('H:i')
                        ]);
                $message = 'تم إنهاء الجلسات المحددة بنجاح.';
                break;
            
            case 'cancel':
                $sessions->whereIn('status', ['scheduled', 'ongoing'])
                        ->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => $validated['cancellation_reason']
                        ]);
                $message = 'تم إلغاء الجلسات المحددة بنجاح.';
                break;
            
            case 'delete':
                // Only allow deletion of non-completed sessions or sessions without attendance
                $sessions->where(function($query) {
                    $query->where('status', '!=', 'completed')
                          ->orWhere('attendance_taken', false);
                })->delete();
                $message = 'تم حذف الجلسات المحددة بنجاح.';
                break;
        }

        return back()->with('success', $message);
    }

    private function calculateAverageAttendance(): float
    {
        $completedSessions = ClassSession::where('status', 'completed')
                                        ->where('attendance_taken', true)
                                        ->get();

        if ($completedSessions->isEmpty()) {
            return 0;
        }

        $totalAttendancePercentage = $completedSessions->sum(function ($session) {
            return $session->attendance_percentage ?? 0;
        });

        return round($totalAttendancePercentage / $completedSessions->count(), 1);
    }

    private function getSessionAttendanceStats(ClassSession $session): array
    {
        $attendances = $session->attendanceSessions;
        $totalStudents = $session->circle->students->count();

        return [
            'total_students' => $totalStudents,
            'present_count' => $attendances->where('status', 'present')->count(),
            'absent_count' => $attendances->where('status', 'absent')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'excused_count' => $attendances->where('status', 'excused')->count(),
            'attendance_percentage' => $totalStudents > 0 ? 
                round(($attendances->whereIn('status', ['present', 'late'])->count() / $totalStudents) * 100, 1) : 0,
            'average_participation' => round($attendances->where('participation_score', '>', 0)->avg('participation_score'), 1)
        ];
    }

    private function updateSessionStats(ClassSession $session): void
    {
        $stats = $this->getSessionAttendanceStats($session);
        
        $session->update([
            'total_students' => $stats['total_students'],
            'present_students' => $stats['present_count'],
            'absent_students' => $stats['absent_count'],
            'attendance_percentage' => $stats['attendance_percentage']
        ]);
    }

    private function sendAttendanceNotifications(ClassSession $session): void
    {
        $absentStudents = $session->attendanceSessions()
                                 ->where('status', 'absent')
                                 ->with('student.parent')
                                 ->get();

        foreach ($absentStudents as $attendance) {
            if ($attendance->student->parent) {
                Notification::create([
                    'user_id' => $attendance->student->parent->id,
                    'title' => 'غياب الطالب',
                    'message' => "لم يحضر الطالب {$attendance->student->name} جلسة {$session->session_title} بتاريخ {$session->session_date->format('Y-m-d')}",
                    'type' => 'absence',
                    'data' => [
                        'student_id' => $attendance->student->id,
                        'session_id' => $session->id,
                        'attendance_id' => $attendance->id,
                        'absence_reason' => $attendance->absence_reason
                    ]
                ]);
            }
        }
    }

    /**
     * Show quick attendance form
     */
    public function quickAttendanceForm()
    {
        $circles = Circle::with('teacher')->active()->get();
        return view('sessions.quick-attendance', compact('circles'));
    }

    /**
     * Store quick attendance
     */
    public function storeQuickAttendance(Request $request)
    {
        $request->validate([
            'circle_id' => 'required|exists:circles,id',
            'session_date' => 'required|date',
            'students' => 'required|array',
            'students.*' => 'exists:students,id'
        ]);

        // Create session
        $session = ClassSession::create([
            'circle_id' => $request->circle_id,
            'session_date' => $request->session_date,
            'session_title' => 'جلسة سريعة',
            'status' => 'completed',
            'attendance_taken' => true
        ]);

        // Record attendance
        foreach ($request->students as $studentId) {
            AttendanceSession::create([
                'session_id' => $session->id,
                'student_id' => $studentId,
                'status' => 'present'
            ]);
        }

        return redirect()->route('sessions.index')->with('success', 'تم تسجيل الحضور بنجاح');
    }

    /**
     * Show weekly sessions
     */
    public function weekly()
    {
        $sessions = ClassSession::with(['circle.teacher'])
            ->whereBetween('session_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->orderBy('session_date')
            ->get();

        return view('sessions.weekly', compact('sessions'));
    }

    /**
     * Show monthly sessions
     */
    public function monthly()
    {
        $sessions = ClassSession::with(['circle.teacher'])
            ->whereBetween('session_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->orderBy('session_date')
            ->get();

        return view('sessions.monthly', compact('sessions'));
    }

    /**
     * Show calendar view
     */
    public function calendar()
    {
        $sessions = ClassSession::with(['circle.teacher'])
            ->whereMonth('session_date', Carbon::now()->month)
            ->get();

        return view('sessions.calendar', compact('sessions'));
    }

    /**
     * تصنيف الجلسات حسب الحالة والوقت
     */
    private function classifySessions($sessions)
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $endOfToday = $now->copy()->endOfDay();

        $classified = [
            'today' => [],
            'upcoming' => [],
            'missed' => [],
            'completed' => []
        ];

        foreach ($sessions as $session) {
            // استخراج الوقت من datetime
            $startTime = $session->circle->start_time ? 
                Carbon::parse($session->circle->start_time)->format('H:i:s') : '00:00:00';
            $endTime = $session->circle->end_time ? 
                Carbon::parse($session->circle->end_time)->format('H:i:s') : '23:59:59';
            
            $sessionDateTime = Carbon::parse($session->session_date)->setTimeFromTimeString($startTime);
            $sessionEndTime = Carbon::parse($session->session_date)->setTimeFromTimeString($endTime);
            
            // تحديد حالة الجلسة
            $status = $this->getSessionStatus($session, $sessionDateTime, $sessionEndTime, $now);
            
            // إضافة معلومات إضافية للجلسة
            $session->status_info = $this->getSessionInfo($session, $status);
            $session->time_info = $this->getTimeInfo($session, $status);
            
            // تصنيف الجلسات حسب الحالة أولاً
            if ($status === 'completed') {
                // الجلسات المكتملة
                $classified['completed'][] = $session;
            } elseif ($status === 'missed') {
                // الجلسات الفائتة
                $classified['missed'][] = $session;
            } elseif ($status === 'upcoming' || $status === 'live') {
                // الجلسات القادمة والجارية
                $classified['upcoming'][] = $session;
            }
            
            // إضافة جلسات اليوم (فقط الجلسات المجدولة لليوم الحالي)
            $sessionDate = Carbon::parse($session->session_date)->startOfDay();
            if ($sessionDate->isSameDay($today)) {
                $classified['today'][] = $session;
            }
        }

        return $classified;
    }

    /**
     * تحديد حالة الجلسة
     */
    private function getSessionStatus($session, $sessionDateTime, $sessionEndTime, $now)
    {
        // التحقق من وجود سجلات حضور
        $hasAttendance = \App\Models\Attendance::where('session_id', $session->id)
            ->exists();

        if ($hasAttendance) {
            return 'completed';
        }

        if ($now->gt($sessionEndTime)) {
            return 'missed';
        } elseif ($now->between($sessionDateTime, $sessionEndTime)) {
            return 'live';
        } else {
            return 'upcoming';
        }
    }

    /**
     * الحصول على معلومات إضافية للجلسة
     */
    private function getSessionInfo($session, $status)
    {
        $info = [
            'status' => $status,
            'color_class' => $this->getColorClass($status),
            'icon' => $this->getIcon($status),
            'badge_text' => $this->getBadgeText($status),
            'action_button' => $this->getActionButton($status),
            'time_info' => $this->getTimeInfo($session, $status)
        ];

        if ($status === 'completed') {
            $info['attendance_stats'] = $this->getAttendanceStats($session);
        }

        return $info;
    }

    /**
     * الحصول على كلاس اللون حسب الحالة
     */
    private function getColorClass($status)
    {
        $colors = [
            'completed' => 'bg-success-light border-success',
            'missed' => 'bg-danger-light border-danger',
            'live' => 'bg-warning-light border-warning',
            'upcoming' => 'bg-info-light border-info'
        ];

        return $colors[$status] ?? 'bg-light border-secondary';
    }

    /**
     * الحصول على الأيقونة حسب الحالة
     */
    private function getIcon($status)
    {
        $icons = [
            'completed' => '<i class="fas fa-check-circle text-success"></i>',
            'missed' => '<i class="fas fa-exclamation-triangle text-danger"></i>',
            'live' => '<i class="fas fa-circle text-danger blink"></i>',
            'upcoming' => '<i class="fas fa-clock text-info"></i>'
        ];

        return $icons[$status] ?? '<i class="fas fa-question-circle text-secondary"></i>';
    }

    /**
     * الحصول على نص الشارة حسب الحالة
     */
    private function getBadgeText($status)
    {
        $badges = [
            'completed' => '<span class="badge bg-success">مكتملة</span>',
            'missed' => '<span class="badge bg-danger">فائتة</span>',
            'live' => '<span class="badge bg-warning text-dark">جارية الآن</span>',
            'upcoming' => '<span class="badge bg-info">قادمة</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">غير محدد</span>';
    }

    /**
     * الحصول على زر الإجراء حسب الحالة
     */
    private function getActionButton($status)
    {
        $buttons = [
            'completed' => [
                'text' => 'عرض الحضور',
                'class' => 'btn btn-outline-success btn-sm',
                'icon' => 'fas fa-eye'
            ],
            'missed' => [
                'text' => 'تسجيل الحضور',
                'class' => 'btn btn-outline-danger btn-sm',
                'icon' => 'fas fa-user-check'
            ],
            'live' => [
                'text' => 'تسجيل الحضور الآن',
                'class' => 'btn btn-warning btn-sm',
                'icon' => 'fas fa-user-plus'
            ],
            'upcoming' => [
                'text' => 'عرض التفاصيل',
                'class' => 'btn btn-outline-info btn-sm',
                'icon' => 'fas fa-info-circle'
            ]
        ];

        return $buttons[$status] ?? [
            'text' => 'عرض',
            'class' => 'btn btn-outline-secondary btn-sm',
            'icon' => 'fas fa-eye'
        ];
    }

    /**
     * الحصول على معلومات الوقت
     */
    private function getTimeInfo($session, $status)
    {
        $now = now();
        $startTime = $session->circle->start_time ?? '00:00:00';
        $endTime = $session->circle->end_time ?? '23:59:59';
        
        $sessionDateTime = Carbon::parse($session->session_date)->setTimeFromTimeString($startTime);
        $sessionEndTime = Carbon::parse($session->session_date)->setTimeFromTimeString($endTime);

        switch ($status) {
            case 'upcoming':
                $diff = $now->diffForHumans($sessionDateTime, true);
                return "خلال {$diff}";
                
            case 'live':
                $remaining = $sessionEndTime->diffForHumans($now, true);
                return "متبقي {$remaining}";
                
            case 'missed':
                $diff = $sessionEndTime->diffForHumans($now, true);
                return "تأخر {$diff}";
                
            case 'completed':
                return "تم في " . $sessionDateTime->format('d/m/Y');
                
            default:
                return $sessionDateTime->format('d/m/Y H:i');
        }
    }

    /**
     * الحصول على جلسات القسم المحدد
     */
    private function getSectionSessions($classifiedSessions, $section)
    {
        switch ($section) {
            case 'today':
                return $classifiedSessions['today'];
            case 'upcoming':
                return $classifiedSessions['upcoming'];
            case 'missed':
                return $classifiedSessions['missed'];
            case 'completed':
                return $classifiedSessions['completed'];
            default:
                return $classifiedSessions['today'];
        }
    }

    /**
     * الحصول على إحصائيات الحضور للجلسة
     */
    private function getAttendanceStats($session)
    {
        $attendances = \App\Models\Attendance::where('session_id', $session->id)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $excused = $attendances->where('status', 'excused')->count();

        $attendanceRate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'attendance_rate' => $attendanceRate
        ];
    }
}

