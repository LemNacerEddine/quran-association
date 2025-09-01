<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'birth_date',
        'gender',
        'address',
        'qualification',
        'experience',
        'specialization',
        'is_active',
        'last_login_at'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function circles()
    {
        return $this->hasMany(Circle::class);
    }

    public function schedules()
    {
        return $this->hasManyThrough(ClassSchedule::class, Circle::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors & Mutators
    public function getActiveCirclesCountAttribute()
    {
        return $this->circles()->active()->count();
    }

    public function getTotalStudentsAttribute()
    {
        return $this->circles()->withCount('students')->get()->sum('students_count');
    }

    // Helper methods for authentication
    public function generatePassword()
    {
        $this->password = substr($this->phone, -4);
        return $this->password;
    }

    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}

