<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsenceReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason_name',
        'reason_description',
        'reason_type',
        'is_excused',
        'requires_documentation',
        'is_active',
        'max_consecutive_days',
        'affects_attendance_record',
        'usage_count'
    ];

    protected $casts = [
        'is_excused' => 'boolean',
        'requires_documentation' => 'boolean',
        'is_active' => 'boolean',
        'affects_attendance_record' => 'boolean',
        'max_consecutive_days' => 'integer',
        'usage_count' => 'integer'
    ];

    // العلاقات
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'absence_reason_id');
    }

    // النطاقات (Scopes)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExcused($query)
    {
        return $query->where('is_excused', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('reason_type', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('reason_name');
    }

    // الدوال المساعدة
    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'medical' => 'طبي',
            'family' => 'عائلي',
            'travel' => 'سفر',
            'emergency' => 'طارئ',
            'personal' => 'شخصي',
            'other' => 'أخرى'
        ];

        return $categories[$this->reason_type] ?? $this->reason_type;
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_excused) {
            return '<span class="badge bg-success">معذور</span>';
        }
        return '<span class="badge bg-warning">غير معذور</span>';
    }

    // إحصائيات الاستخدام
    public function getUsageStatsAttribute(): array
    {
        $total = $this->attendanceSessions()->count();
        $thisMonth = $this->attendanceSessions()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        return [
            'total_usage' => $total,
            'this_month' => $thisMonth,
            'percentage_this_month' => $total > 0 ? round(($thisMonth / $total) * 100, 2) : 0
        ];
    }

    // الأسباب الافتراضية
    public static function getDefaultReasons(): array
    {
        return [
            [
                'reason_name' => 'مرض',
                'reason_description' => 'غياب بسبب المرض',
                'reason_type' => 'medical',
                'is_excused' => true,
                'requires_documentation' => true,
                'is_active' => true,
                'affects_attendance_record' => false,
                'max_consecutive_days' => 7
            ],
            [
                'reason_name' => 'ظرف عائلي طارئ',
                'reason_description' => 'غياب بسبب ظرف عائلي طارئ',
                'reason_type' => 'family',
                'is_excused' => true,
                'requires_documentation' => false,
                'is_active' => true,
                'affects_attendance_record' => false,
                'max_consecutive_days' => 3
            ],
            [
                'reason_name' => 'سفر',
                'reason_description' => 'غياب بسبب السفر',
                'reason_type' => 'travel',
                'is_excused' => true,
                'requires_documentation' => false,
                'is_active' => true,
                'affects_attendance_record' => false,
                'max_consecutive_days' => 14
            ],
            [
                'reason_name' => 'ظرف شخصي',
                'reason_description' => 'غياب بسبب ظرف شخصي',
                'reason_type' => 'personal',
                'is_excused' => true,
                'requires_documentation' => false,
                'is_active' => true,
                'affects_attendance_record' => false,
                'max_consecutive_days' => 2
            ],
            [
                'reason_name' => 'غياب بدون عذر',
                'reason_description' => 'غياب بدون سبب مقبول',
                'reason_type' => 'other',
                'is_excused' => false,
                'requires_documentation' => false,
                'is_active' => true,
                'affects_attendance_record' => true,
                'max_consecutive_days' => 1
            ]
        ];
    }

    // إنشاء الأسباب الافتراضية
    public static function createDefaultReasons(): void
    {
        foreach (self::getDefaultReasons() as $reason) {
            self::firstOrCreate(
                ['reason_name' => $reason['reason_name']],
                $reason
            );
        }
    }
}

