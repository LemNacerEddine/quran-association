<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'schedule_name',
        'description',
        'day_of_week',
        'start_time',
        'end_time',
        'session_type',
        'location',
        'is_active',
        'start_date',
        'end_date',
        'recurring_pattern',
        'notes',
        'has_multiple_days',
        'default_settings'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'has_multiple_days' => 'boolean',
        'recurring_pattern' => 'array',
        'default_settings' => 'array'
    ];

    // العلاقات
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'schedule_id');
    }

    public function scheduleDays(): HasMany
    {
        return $this->hasMany(ScheduleDay::class, 'schedule_id');
    }

    public function activeDays(): HasMany
    {
        return $this->hasMany(ScheduleDay::class, 'schedule_id')->where('is_active', true);
    }

    // الدوال المساعدة
    public function getDayNameAttribute(): string
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

        return $days[$this->day_of_week] ?? ($this->day_of_week ?? 'غير محدد');
    }

    public function getSessionTypeNameAttribute(): string
    {
        $types = [
            'morning' => 'صباحية',
            'afternoon' => 'ظهيرة',
            'evening' => 'مسائية'
        ];

        return $types[$this->session_type] ?? ($this->session_type ?? 'غير محدد');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    // التحقق من التعارض في الأوقات
    public function hasTimeConflict($circleId = null, $excludeId = null): bool
    {
        $query = self::where('day_of_week', $this->day_of_week)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('start_time', '<=', $this->start_time)
                         ->where('end_time', '>', $this->start_time);
                })->orWhere(function ($subQ) {
                    $subQ->where('start_time', '<', $this->end_time)
                         ->where('end_time', '>=', $this->end_time);
                })->orWhere(function ($subQ) {
                    $subQ->where('start_time', '>=', $this->start_time)
                         ->where('end_time', '<=', $this->end_time);
                });
            });

        if ($circleId) {
            $query->where('circle_id', $circleId);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // الحصول على الجلسة التالية
    public function getNextSession()
    {
        return $this->sessions()
            ->where('session_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('session_date')
            ->first();
    }

    // إنشاء جلسات تلقائية للأسبوع
    public function generateWeeklySessions($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now();
        $endDate = $endDate ?? now()->addWeeks(4);

        $sessions = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if ($current->format('l') === ucfirst($this->day_of_week)) {
                $sessions[] = [
                    'schedule_id' => $this->id,
                    'circle_id' => $this->circle_id,
                    'teacher_id' => $this->circle->teacher_id,
                    'session_title' => $this->schedule_name . ' - ' . $current->format('Y-m-d'),
                    'session_date' => $current->toDateString(),
                    'status' => 'scheduled',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            $current->addDay();
        }

        if (!empty($sessions)) {
            ClassSession::insert($sessions);
        }

        return count($sessions);
    }

    // دوال جديدة لدعم الأيام المتعددة
    
    /**
     * الحصول على جميع أيام الجدولة (سواء من الحقل القديم أو الجدول الجديد)
     */
    public function getAllDays()
    {
        if ($this->has_multiple_days && $this->scheduleDays()->exists()) {
            return $this->scheduleDays()->active()->get();
        }
        
        // إرجاع اليوم الواحد كمجموعة للتوافق
        if ($this->day_of_week) {
            return collect([
                (object) [
                    'day_of_week' => $this->day_of_week,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'session_type' => $this->session_type,
                    'location' => $this->location,
                    'day_name' => $this->day_name,
                    'session_type_name' => $this->session_type_name
                ]
            ]);
        }
        
        return collect();
    }

    /**
     * إضافة يوم جديد للجدولة
     */
    public function addDay($dayData)
    {
        // تحويل الجدولة لنظام الأيام المتعددة إذا لم تكن كذلك
        if (!$this->has_multiple_days) {
            $this->convertToMultipleDays();
        }

        return $this->scheduleDays()->create($dayData);
    }

    /**
     * تحويل الجدولة من يوم واحد إلى أيام متعددة
     */
    public function convertToMultipleDays()
    {
        if ($this->has_multiple_days) {
            return; // مُحولة مسبقاً
        }

        // إنشاء سجل في جدول الأيام للبيانات الحالية
        if ($this->day_of_week) {
            $this->scheduleDays()->create([
                'day_of_week' => $this->day_of_week,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'session_type' => $this->session_type,
                'location' => $this->location,
                'is_active' => true
            ]);
        }

        // تحديث الجدولة لتصبح متعددة الأيام
        $this->update(['has_multiple_days' => true]);
    }

    /**
     * فحص التعارضات للأيام المتعددة
     */
    public function hasMultipleDaysConflict($excludeId = null)
    {
        $conflicts = [];
        
        foreach ($this->getAllDays() as $day) {
            $conflict = ScheduleDay::where('schedule_id', '!=', $this->id)
                ->where('day_of_week', $day->day_of_week)
                ->where('is_active', true)
                ->where(function ($q) use ($day) {
                    $q->whereBetween('start_time', [$day->start_time, $day->end_time])
                      ->orWhereBetween('end_time', [$day->start_time, $day->end_time])
                      ->orWhere(function ($subQ) use ($day) {
                          $subQ->where('start_time', '<=', $day->start_time)
                               ->where('end_time', '>=', $day->end_time);
                      });
                });

            if ($excludeId) {
                $conflict->where('id', '!=', $excludeId);
            }

            if ($conflict->exists()) {
                $conflicts[] = [
                    'day' => $day->day_of_week,
                    'time' => $day->start_time . ' - ' . $day->end_time,
                    'conflict' => $conflict->first()
                ];
            }
        }

        return $conflicts;
    }

    /**
     * الحصول على ملخص الأيام والأوقات
     */
    public function getDaysSummary()
    {
        $days = $this->getAllDays();
        
        if ($days->isEmpty()) {
            return 'غير محدد';
        }

        if ($days->count() === 1) {
            $day = $days->first();
            return $day->day_name . ' (' . $day->start_time . ' - ' . $day->end_time . ')';
        }

        $summary = $days->map(function ($day) {
            return $day->day_name . ' (' . $day->start_time . ' - ' . $day->end_time . ')';
        })->join('، ');

        return $summary;
    }

    /**
     * إنشاء جلسات للأيام المتعددة
     */
    public function generateMultipleDaysSessions($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now();
        $endDate = $endDate ?? now()->addWeeks(4);
        
        $sessions = [];
        $days = $this->getAllDays();
        
        foreach ($days as $day) {
            $current = $startDate->copy();
            
            while ($current <= $endDate) {
                $dayName = strtolower($current->format('l'));
                
                if ($dayName === $day->day_of_week) {
                    $sessions[] = [
                        'schedule_id' => $this->id,
                        'session_date' => $current->toDateString(),
                        'start_time' => $day->start_time,
                        'end_time' => $day->end_time,
                        'session_type' => $day->session_type ?? 'regular',
                        'location' => $day->location ?? $this->location,
                        'status' => 'scheduled',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                $current->addDay();
            }
        }
        
        if (!empty($sessions)) {
            ClassSession::insert($sessions);
        }
        
        return count($sessions);
    }
}

