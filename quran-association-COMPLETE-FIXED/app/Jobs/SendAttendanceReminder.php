<?php

namespace App\Jobs;

use App\Models\ClassSession;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SendAttendanceReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;

    public function __construct(ClassSession $session)
    {
        $this->session = $session;
    }

    public function handle(): void
    {
        // Get all parents of students in this session's circle
        $parents = User::where('role', 'parent')
                      ->whereHas('children', function($query) {
                          $query->where('circle_id', $this->session->circle_id);
                      })
                      ->get();

        foreach ($parents as $parent) {
            // Check if parent wants attendance reminders
            $settings = $parent->notificationSettings()
                              ->where('notification_type', 'reminder')
                              ->first();

            if ($settings && $settings->is_enabled) {
                Notification::create([
                    'user_id' => $parent->id,
                    'title' => 'تذكير بموعد الحصة',
                    'message' => "تذكير: ستبدأ حصة {$this->session->session_title} في {$this->session->session_date->format('Y-m-d')} الساعة " . ($this->session->actual_start_time ?? '08:00'),
                    'type' => 'attendance_reminder',
                    'data' => [
                        'session_id' => $this->session->id,
                        'circle_id' => $this->session->circle_id,
                        'session_date' => $this->session->session_date->format('Y-m-d'),
                        'session_time' => $this->session->actual_start_time ?? '08:00'
                    ]
                ]);
            }
        }
    }
}

