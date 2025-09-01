<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Session extends Model
{
    use HasFactory;

    protected $table = 'class_sessions';

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
        'attendance_taken_at' => 'datetime'
    ];

    // العلاقات
    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'attendance')
                    ->withPivot('status', 'points', 'notes', 'marked_at')
                    ->withTimestamps();
    }

    // Accessors for backward compatibility
    public function getTitleAttribute()
    {
        return $this->session_title;
    }

    public function getDescriptionAttribute()
    {
        return $this->session_description;
    }

    public function getStartTimeAttribute()
    {
        return $this->actual_start_time;
    }

    public function getEndTimeAttribute()
    {
        return $this->actual_end_time;
    }

    public function getNotesAttribute()
    {
        return $this->session_notes;
    }

    public function getDateAttribute()
    {
        return $this->session_date;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>', today())
                    ->where('status', 'scheduled');
    }

    public function scopePast($query)
    {
        return $query->where('session_date', '<', today())
                    ->orWhere(function($q) {
                        $q->whereDate('session_date', today())
                          ->whereTime('end_time', '<', now()->format('H:i:s'));
                    });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'scheduled' => 'مجدولة',
            'ongoing' => 'جارية',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغية',
            'postponed' => 'مؤجلة'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled' => 'primary',
            'ongoing' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'postponed' => 'secondary'
        ];

        return $colors[$this->status] ?? 'primary';
    }

    public function getCanStartAttribute()
    {
        return $this->status === 'scheduled' && 
               $this->session_date->isToday() && 
               now()->format('H:i') >= $this->start_time->format('H:i');
    }

    public function getCanEndAttribute()
    {
        return $this->status === 'ongoing';
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->ended_at) {
            return $this->started_at->diffInMinutes($this->ended_at);
        }
        
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    // Helper methods
    public function startSession()
    {
        $this->update([
            'status' => 'ongoing',
            'started_at' => now()
        ]);
    }

    public function endSession()
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now()
        ]);
    }

    public function postponeSession($newDate, $reason = null)
    {
        $this->update([
            'status' => 'postponed',
            'session_date' => $newDate,
            'notes' => $reason
        ]);
    }

    public function cancelSession($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason
        ]);
    }

    public function getAttendanceStats()
    {
        $attendance = $this->attendance;
        
        return [
            'total' => $attendance->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'excused' => $attendance->where('status', 'excused')->count(),
            'total_points' => $attendance->sum('points')
        ];
    }
}

