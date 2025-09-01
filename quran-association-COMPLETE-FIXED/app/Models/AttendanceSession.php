<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id',
        'circle_id',
        'status',
        'arrival_time',
        'departure_time',
        'absence_reason',
        'notes',
        'parent_notified',
        'parent_notified_at',
        'notification_method',
        'recorded_by',
        'recorded_at',
        'is_makeup_session',
        'makeup_for_session',
        'participation_score',
        'behavior_notes'
    ];

    protected $casts = [
        'arrival_time' => 'datetime:H:i',
        'departure_time' => 'datetime:H:i',
        'parent_notified' => 'boolean',
        'parent_notified_at' => 'datetime',
        'recorded_at' => 'datetime',
        'is_makeup_session' => 'boolean',
        'participation_score' => 'decimal:1'
    ];

    // العلاقات
    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function makeupForSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'makeup_for_session');
    }

    // الدوال المساعدة
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'غياب بعذر'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'excused' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getFormattedArrivalTimeAttribute(): ?string
    {
        return $this->arrival_time ? $this->arrival_time->format('H:i') : null;
    }

    public function getFormattedDepartureTimeAttribute(): ?string
    {
        return $this->departure_time ? $this->departure_time->format('H:i') : null;
    }

    // تسجيل الوصول
    public function markArrival($arrivalTime = null): void
    {
        $this->arrival_time = $arrivalTime ?? now()->format('H:i');
        $this->status = 'present';
        $this->recorded_at = now();
        $this->recorded_by = auth()->id();

        // تحديد إذا كان متأخراً
        if ($this->session && $this->session->schedule) {
            $scheduledTime = $this->session->schedule->start_time;
            if ($this->arrival_time > $scheduledTime) {
                $this->status = 'late';
            }
        }

        $this->save();

        // تحديث إحصائيات الجلسة
        $this->session->updateAttendanceStats();
    }

    // تسجيل الانصراف
    public function markDeparture($departureTime = null): void
    {
        $this->departure_time = $departureTime ?? now()->format('H:i');
        $this->save();
    }

    // تسجيل الغياب
    public function markAbsent($reason = null): void
    {
        $this->status = 'absent';
        $this->absence_reason = $reason;
        $this->recorded_at = now();
        $this->recorded_by = auth()->id();
        $this->save();

        // تحديث إحصائيات الجلسة
        $this->session->updateAttendanceStats();

        // إرسال إشعار لولي الأمر
        $this->sendAbsenceNotification();
    }

    // إرسال إشعار الغياب
    public function sendAbsenceNotification(): void
    {
        if ($this->student->parent && $this->status === 'absent' && !$this->parent_notified) {
            Notification::create([
                'user_id' => $this->student->parent->id,
                'title' => 'غياب الطالب',
                'message' => "لم يحضر الطالب {$this->student->name} جلسة {$this->session->session_title}",
                'type' => 'absence',
                'data' => [
                    'student_id' => $this->student->id,
                    'session_id' => $this->session->id,
                    'attendance_id' => $this->id,
                    'absence_reason' => $this->absence_reason
                ]
            ]);

            $this->update([
                'parent_notified' => true,
                'parent_notified_at' => now(),
                'notification_method' => 'app'
            ]);
        }
    }

    // تقييم المشاركة
    public function setParticipationScore(float $score): void
    {
        $this->participation_score = max(0, min(10, $score)); // من 0 إلى 10
        $this->save();
    }

    // الحصول على تقرير مفصل
    public function getDetailedReport(): array
    {
        return [
            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'circle' => $this->circle->name
            ],
            'session' => [
                'title' => $this->session->session_title,
                'date' => $this->session->session_date->format('Y-m-d'),
                'scheduled_time' => $this->session->schedule ? $this->session->schedule->formatted_time : null
            ],
            'attendance' => [
                'status' => $this->status,
                'status_name' => $this->status_name,
                'arrival_time' => $this->formatted_arrival_time,
                'departure_time' => $this->formatted_departure_time
            ],
            'absence' => [
                'reason' => $this->absence_reason
            ],
            'performance' => [
                'participation_score' => $this->participation_score
            ],
            'notes' => $this->notes,
            'behavior_notes' => $this->behavior_notes,
            'recorded_by' => $this->recordedBy ? $this->recordedBy->name : null,
            'recorded_at' => $this->recorded_at ? $this->recorded_at->format('Y-m-d H:i') : null
        ];
    }

    // إحصائيات الطالب
    public static function getStudentStats($studentId, $startDate = null, $endDate = null): array
    {
        $query = self::where('student_id', $studentId);

        if ($startDate) {
            $query->whereHas('session', function($q) use ($startDate) {
                $q->where('session_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('session', function($q) use ($endDate) {
                $q->where('session_date', '<=', $endDate);
            });
        }

        $attendances = $query->get();
        $total = $attendances->count();

        if ($total === 0) {
            return [
                'total_sessions' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'excused_count' => 0,
                'attendance_percentage' => 0,
                'average_participation' => 0
            ];
        }

        return [
            'total_sessions' => $total,
            'present_count' => $attendances->where('status', 'present')->count(),
            'absent_count' => $attendances->where('status', 'absent')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'excused_count' => $attendances->where('status', 'excused')->count(),
            'attendance_percentage' => round(($attendances->whereIn('status', ['present', 'late'])->count() / $total) * 100, 2),
            'average_participation' => round($attendances->where('participation_score', '>', 0)->avg('participation_score'), 2)
        ];
    }
}

