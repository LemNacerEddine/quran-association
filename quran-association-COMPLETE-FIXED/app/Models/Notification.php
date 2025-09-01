<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
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
        'title',
        'body',
        'data',
        'status',
        'sent_at',
        'read_at',
        'fcm_message_id',
        'priority',
        'scheduled_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    /**
     * Check if notification is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if notification is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereIn('status', ['pending', 'sent', 'delivered']);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope for active notifications (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for scheduled notifications
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'pending')
                    ->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '>', now());
    }

    /**
     * Scope for due notifications (ready to be sent)
     */
    public function scopeDue($query)
    {
        return $query->where('status', 'pending')
                    ->where(function ($q) {
                        $q->whereNull('scheduled_at')
                          ->orWhere('scheduled_at', '<=', now());
                    });
    }
}

