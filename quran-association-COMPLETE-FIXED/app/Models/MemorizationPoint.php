<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorizationPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'session_type',
        'surah_name',
        'from_verse',
        'to_verse',
        'points',
        'memorized_content',
        'teacher_notes',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'date' => 'date',
        'recorded_at' => 'datetime',
        'points' => 'integer',
    ];

    /**
     * Get the student for this memorization point
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who recorded this point
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update student's total points when a new point is created
        static::created(function ($memorizationPoint) {
            $memorizationPoint->student->updateTotalPoints();
            
            // Send notification to parent
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->sendProgressNotification(
                $memorizationPoint->student, 
                $memorizationPoint
            );
        });

        // Update student's total points when a point is updated
        static::updated(function ($memorizationPoint) {
            $memorizationPoint->student->updateTotalPoints();
        });

        // Update student's total points when a point is deleted
        static::deleted(function ($memorizationPoint) {
            $memorizationPoint->student->updateTotalPoints();
        });
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by session type
     */
    public function scopeForSession($query, $sessionType)
    {
        return $query->where('session_type', $sessionType);
    }
}
