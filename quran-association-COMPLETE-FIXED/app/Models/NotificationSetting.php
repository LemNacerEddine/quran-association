<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'enabled',
        'push_enabled',
        'email_enabled',
        'sms_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'frequency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
    ];

    /**
     * Get the user that owns the notification setting
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notifications are enabled for this type
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if push notifications are enabled
     */
    public function isPushEnabled(): bool
    {
        return $this->enabled && $this->push_enabled;
    }

    /**
     * Check if email notifications are enabled
     */
    public function isEmailEnabled(): bool
    {
        return $this->enabled && $this->email_enabled;
    }

    /**
     * Check if SMS notifications are enabled
     */
    public function isSmsEnabled(): bool
    {
        return $this->enabled && $this->sms_enabled;
    }

    /**
     * Check if current time is within quiet hours
     */
    public function isQuietTime(): bool
    {
        if (!$this->quiet_hours_start || !$this->quiet_hours_end) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start->format('H:i');
        $end = $this->quiet_hours_end->format('H:i');

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        } else {
            // Quiet hours span midnight
            return $now >= $start || $now <= $end;
        }
    }

    /**
     * Scope for enabled settings
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}

