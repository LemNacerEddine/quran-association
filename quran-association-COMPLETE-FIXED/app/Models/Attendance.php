<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'points',
        'memorization_points',
        'final_points',
        'notes',
        'marked_at',
        'recorded_by'
    ];

    protected $casts = [
        'marked_at' => 'datetime'
    ];

    // العلاقات
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'غياب بعذر'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'excused' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusIconAttribute()
    {
        $icons = [
            'present' => 'fas fa-check-circle',
            'absent' => 'fas fa-times-circle',
            'late' => 'fas fa-clock',
            'excused' => 'fas fa-info-circle'
        ];

        return $icons[$this->status] ?? 'fas fa-question-circle';
    }

    public function getIsPositiveAttribute()
    {
        return in_array($this->status, ['present', 'late']);
    }

    // Helper methods
    public static function calculateAttendancePoints($status)
    {
        switch ($status) {
            case 'present':
                return 2; // نقطتان للحضور
            case 'late':
                return 1; // نقطة واحدة للتأخير
            case 'absent':
            case 'excused':
            default:
                return 0; // صفر للغياب
        }
    }

    public static function calculateFinalPoints($attendancePoints, $memorizationPoints, $status)
    {
        // إذا كان الطالب غائب، النقطة النهائية = 0
        if ($status === 'absent') {
            return 0;
        }
        
        // النقطة النهائية = نقطة الحضور + نقطة الحفظ
        return $attendancePoints + $memorizationPoints;
    }

    public function updatePoints($memorizationPoints = 0)
    {
        // حساب نقاط الحضور
        $this->points = self::calculateAttendancePoints($this->status);
        
        // تحديث نقاط الحفظ
        $this->memorization_points = $memorizationPoints;
        
        // حساب النقطة النهائية
        $this->final_points = self::calculateFinalPoints(
            $this->points, 
            $this->memorization_points, 
            $this->status
        );
        
        $this->save();
    }
}

