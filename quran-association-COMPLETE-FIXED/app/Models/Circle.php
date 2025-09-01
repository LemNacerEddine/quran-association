<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'max_students',
        'location',
        'schedule_days',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_students' => 'integer',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_circles')
            ->withPivot(['enrolled_at', 'left_at', 'is_active', 'notes'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    // جميع الطلاب (حتى غير النشطين)
    public function allStudents()
    {
        return $this->belongsToMany(Student::class, 'student_circles')
            ->withPivot(['enrolled_at', 'left_at', 'is_active', 'notes'])
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors & Mutators
    public function getCurrentStudentsAttribute()
    {
        return $this->students()->count();
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->max_students - $this->current_students;
    }

    public function getIsFullAttribute()
    {
        return $this->current_students >= $this->max_students;
    }

    public function getScheduleDaysStringAttribute()
    {
        if (!$this->schedule_days) return 'غير محدد';
        
        return $this->schedule_days;
    }
}

