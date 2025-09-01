<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'circle_id',
        'teacher_id',
        'session_title',
        'session_description',
        'session_date',
        'actual_start_time',
        'actual_end_time',
        'status',
        'lesson_content',
        'homework',
        'session_notes',
        'total_students',
        'present_students',
        'absent_students',
        'attendance_percentage',
        'attendance_taken',
        'attendance_taken_at',
        'attendance_taken_by',
        'cancellation_reason'
    ];

    protected $casts = [
        'session_date' => 'date',
        'actual_start_time' => 'datetime:H:i',
        'actual_end_time' => 'datetime:H:i',
        'attendance_taken' => 'boolean',
        'attendance_taken_at' => 'datetime',
        'attendance_percentage' => 'decimal:2'
    ];

    // العلاقات
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'schedule_id');
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'session_id');
    }

    public function attendanceTakenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendance_taken_by');
    }

    // الدوال المساعدة
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'scheduled' => 'مجدولة',
            'ongoing' => 'جارية',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغية',
            'missed' => 'فائتة',
            'attendance_taken' => 'في انتظار النقاط',
            'pending' => 'في الانتظار'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // التحقق من حالة الجلسة
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isAttendanceTaken(): bool
    {
        return $this->status === 'attendance_taken' || $this->status === 'completed';
    }

    public function isPendingPoints(): bool
    {
        return $this->status === 'attendance_taken';
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->session_date->format('Y-m-d');
    }

    public function getFormattedTimeAttribute(): string
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            return $this->actual_start_time->format('H:i') . ' - ' . $this->actual_end_time->format('H:i');
        }
        
        if ($this->schedule) {
            return $this->schedule->formatted_time;
        }

        return 'غير محدد';
    }

    public function getDurationInMinutesAttribute(): ?int
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            return $this->actual_start_time->diffInMinutes($this->actual_end_time);
        }

        if ($this->schedule) {
            return $this->schedule->duration_in_minutes;
        }

        return null;
    }

    // تسجيل الحضور التلقائي
    public function initializeAttendance(): void
    {
        if ($this->attendance_taken) {
            return;
        }

        $students = $this->circle->students;
        $this->total_students = $students->count();

        foreach ($students as $student) {
            AttendanceSession::create([
                'session_id' => $this->id,
                'student_id' => $student->id,
                'circle_id' => $this->circle_id,
                'attendance_date' => $this->session_date,
                'status' => 'present', // افتراضياً الكل حاضر
                'marked_by' => auth()->id(),
                'marked_at' => now()
            ]);
        }

        $this->updateAttendanceStats();
    }

    // تحديث إحصائيات الحضور
    public function updateAttendanceStats(): void
    {
        $attendances = $this->attendanceSessions;
        
        $this->total_students = $attendances->count();
        $this->present_students = $attendances->where('status', 'present')->count();
        $this->absent_students = $attendances->where('status', 'absent')->count();
        
        if ($this->total_students > 0) {
            $this->attendance_percentage = ($this->present_students / $this->total_students) * 100;
        }

        $this->save();
    }

    // إنهاء الجلسة
    public function completeSession(): void
    {
        $this->status = 'completed';
        $this->actual_end_time = now()->format('H:i');
        
        if (!$this->actual_start_time) {
            $this->actual_start_time = $this->schedule ? 
                $this->schedule->start_time->format('H:i') : 
                now()->subHour()->format('H:i');
        }

        $this->save();

        // إرسال إشعارات للغائبين
        $this->sendAbsenceNotifications();
    }

    // إرسال إشعارات الغياب
    public function sendAbsenceNotifications(): void
    {
        $absentStudents = $this->attendanceSessions()
            ->where('status', 'absent')
            ->with('student.parent')
            ->get();

        foreach ($absentStudents as $attendance) {
            if ($attendance->student->parent) {
                // إرسال إشعار لولي الأمر
                Notification::create([
                    'user_id' => $attendance->student->parent->id,
                    'title' => 'غياب الطالب',
                    'message' => "لم يحضر الطالب {$attendance->student->name} حصة {$this->session_title} بتاريخ {$this->formatted_date}",
                    'type' => 'absence',
                    'data' => [
                        'student_id' => $attendance->student->id,
                        'session_id' => $this->id,
                        'absence_reason' => $attendance->absence_reason
                    ]
                ]);
            }
        }
    }

    // بدء الجلسة
    public function startSession(): void
    {
        $this->status = 'ongoing';
        $this->actual_start_time = now()->format('H:i');
        $this->save();

        // تهيئة الحضور إذا لم يتم بعد
        if (!$this->attendance_taken) {
            $this->initializeAttendance();
        }
    }

    // إلغاء الجلسة
    public function cancelSession(string $reason): void
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->save();

        // إشعار الطلاب وأولياء الأمور بالإلغاء
        $this->sendCancellationNotifications();
    }

    // إرسال إشعارات الإلغاء
    public function sendCancellationNotifications(): void
    {
        $students = $this->circle->students()->with('parent')->get();

        foreach ($students as $student) {
            if ($student->parent) {
                Notification::create([
                    'user_id' => $student->parent->id,
                    'title' => 'إلغاء الحصة',
                    'message' => "تم إلغاء حصة {$this->session_title} بتاريخ {$this->formatted_date}. السبب: {$this->cancellation_reason}",
                    'type' => 'cancellation',
                    'data' => [
                        'student_id' => $student->id,
                        'session_id' => $this->id,
                        'reason' => $this->cancellation_reason
                    ]
                ]);
            }
        }
    }

    // التحقق من إمكانية تعديل الحضور
    public function canEditAttendance(): bool
    {
        return in_array($this->status, ['scheduled', 'ongoing']) || 
               ($this->status === 'completed' && $this->session_date->isToday());
    }

    // الحصول على تقرير الحضور
    public function getAttendanceReport(): array
    {
        $attendances = $this->attendanceSessions()->with('student')->get();

        return [
            'session_info' => [
                'title' => $this->session_title,
                'date' => $this->formatted_date,
                'time' => $this->formatted_time,
                'circle' => $this->circle->name,
                'teacher' => $this->teacher->name
            ],
            'statistics' => [
                'total_students' => $this->total_students,
                'present_students' => $this->present_students,
                'absent_students' => $this->absent_students,
                'attendance_percentage' => round($this->attendance_percentage, 2)
            ],
            'attendances' => $attendances->map(function ($attendance) {
                return [
                    'student_name' => $attendance->student->name,
                    'status' => $attendance->status,
                    'status_name' => $attendance->status_name,
                    'arrival_time' => $attendance->arrival_time,
                    'absence_reason' => $attendance->absence_reason,
                    'notes' => $attendance->notes
                ];
            })
        ];
    }
}

