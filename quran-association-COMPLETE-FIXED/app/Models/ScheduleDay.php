<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'day_of_week',
        'start_time',
        'end_time',
        'session_type',
        'location',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function schedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'schedule_id');
    }

    // الدوال المساعدة
    public function getDayNameAttribute()
    {
        $days = [
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت'
        ];

        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    public function getSessionTypeNameAttribute()
    {
        $types = [
            'morning' => 'صباحية',
            'afternoon' => 'ظهيرة',
            'evening' => 'مسائية'
        ];

        return $types[$this->session_type] ?? $this->session_type;
    }

    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            
            $diff = $end->diffInMinutes($start);
            $hours = floor($diff / 60);
            $minutes = $diff % 60;
            
            $duration = '';
            if ($hours > 0) {
                $duration .= $hours . ' ساعة ';
            }
            if ($minutes > 0) {
                $duration .= $minutes . ' دقيقة';
            }
            
            return trim($duration);
        }
        
        return null;
    }

    // فحص التعارضات
    public function hasConflictWith($scheduleId, $dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        $query = self::where('schedule_id', '!=', $scheduleId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($subQ) use ($startTime, $endTime) {
                      $subQ->where('start_time', '<=', $startTime)
                           ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    public function scopeForSchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }
}