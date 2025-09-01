<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'phone',
        'parent_phone',
        'birth_date',
        'gender',
        'address',
        'education_level',
        'notes',
        'is_active',
        'circle_id'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'age' => 'integer',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function circles()
    {
        return $this->belongsToMany(Circle::class, 'student_circles')
            ->withPivot(['enrolled_at', 'left_at', 'is_active', 'notes'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    // جميع الحلقات (حتى غير النشطة)
    public function allCircles()
    {
        return $this->belongsToMany(Circle::class, 'student_circles')
            ->withPivot(['enrolled_at', 'left_at', 'is_active', 'notes'])
            ->withTimestamps();
    }

    // العلاقة القديمة للتوافق مع الكود الموجود
    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * علاقة many-to-many مع أولياء الأمور
     */
    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
                    ->withPivot('relationship_type', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * الحصول على الحلقة الحالية للطالب
     */
    public function getCurrentCircleAttribute()
    {
        return $this->circles()->wherePivot('is_active', 1)->first();
    }
    
    /**
     * علاقة مع الحلقة الحالية (للتوافق مع الكود القديم)
     */
    public function getCircleAttribute()
    {
        return $this->current_circle;
    }

    /**
     * الحصول على ولي الأمر الأساسي
     */
    public function primaryGuardian()
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
                    ->wherePivot('is_primary', true)
                    ->withPivot('relationship_type', 'is_primary')
                    ->withTimestamps()
                    ->first();
    }

    /**
     * الحصول على جميع أولياء الأمور النشطين
     */
    public function activeGuardians()
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
                    ->where('guardians.is_active', true)
                    ->withPivot('relationship_type', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * تحديث إجمالي النقاط للطالب
     */
    public function updateTotalPoints()
    {
        // يمكن إضافة منطق تحديث النقاط هنا إذا لزم الأمر
        // حالياً نتركها فارغة لتجنب الأخطاء
        return true;
    }


}

