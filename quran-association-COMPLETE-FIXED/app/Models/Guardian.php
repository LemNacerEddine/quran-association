<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guardian extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'national_id',
        'address',
        'job',
        'access_code',
        'relationship',
        'relationship_type',
        'is_active',
        'notes',
        'last_login_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * علاقة many-to-many مع الطلاب
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
                    ->withPivot('relationship_type', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * الحصول على الطلاب الأساسيين لولي الأمر
     */
    public function primaryStudents()
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
                    ->wherePivot('is_primary', true)
                    ->withPivot('relationship_type', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * إنشاء كود الدخول تلقائياً من آخر 4 أرقام من الهاتف
     */
    public static function generateAccessCode($phone)
    {
        // إزالة أي رموز غير رقمية
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // أخذ آخر 4 أرقام
        return substr($cleanPhone, -4);
    }

    /**
     * تحديث كود الدخول عند تغيير رقم الهاتف
     */
    public function updateAccessCode()
    {
        $this->access_code = self::generateAccessCode($this->phone);
        $this->save();
    }

    /**
     * التحقق من كود الدخول
     */
    public function verifyAccessCode($code)
    {
        return $this->access_code === $code;
    }

    /**
     * الحصول على نص صلة القرابة
     */
    public function getRelationshipTextAttribute()
    {
        $relationships = [
            'father' => 'الأب',
            'mother' => 'الأم',
            'guardian' => 'ولي الأمر',
            'other' => 'أخرى'
        ];

        return $relationships[$this->relationship] ?? 'غير محدد';
    }

    /**
     * الحصول على حالة النشاط كنص
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    /**
     * scope للأولياء النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * scope للبحث
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%");
        });
    }
}
