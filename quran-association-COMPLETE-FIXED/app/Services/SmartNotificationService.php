<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\ClassSession;
use App\Models\AttendanceSession;
use App\Models\MemorizationPoint;
use App\Jobs\SendSMSNotification;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmartNotificationService
{
    const NOTIFICATION_TYPES = [
        'attendance_reminder' => 'تذكير الحضور',
        'absence_alert' => 'تنبيه الغياب',
        'progress_update' => 'تحديث التقدم',
        'achievement' => 'إنجاز',
        'schedule_change' => 'تغيير الجدول',
        'payment_reminder' => 'تذكير الدفع',
        'weekly_report' => 'التقرير الأسبوعي',
        'monthly_report' => 'التقرير الشهري',
        'emergency' => 'طوارئ',
        'general_announcement' => 'إعلان عام'
    ];

    const NOTIFICATION_CHANNELS = [
        'sms' => 'رسائل نصية',
        'email' => 'بريد إلكتروني',
        'push' => 'إشعارات فورية',
        'in_app' => 'داخل التطبيق'
    ];

    const PRIORITY_LEVELS = [
        'low' => 1,
        'normal' => 2,
        'high' => 3,
        'urgent' => 4,
        'emergency' => 5
    ];

    public function sendAttendanceReminder($session, $reminderTime = 30)
    {
        $circle = $session->schedule->circle;
        $students = $circle->students()->where('is_active', true)->get();

        foreach ($students as $student) {
            $parent = $student->parent;
            if (!$parent) continue;

            $settings = $this->getUserNotificationSettings($parent, 'attendance_reminder');
            if (!$settings['enabled']) continue;

            $message = $this->buildAttendanceReminderMessage($student, $session, $reminderTime);
            
            $this->sendNotification([
                'user_id' => $parent->id,
                'type' => 'attendance_reminder',
                'title' => 'تذكير حضور الحصة',
                'message' => $message,
                'data' => [
                    'student_id' => $student->id,
                    'session_id' => $session->id,
                    'circle_id' => $circle->id,
                    'reminder_time' => $reminderTime
                ],
                'channels' => $settings['channels'],
                'priority' => 'normal',
                'scheduled_at' => Carbon::parse($session->session_date . ' ' . $session->actual_start_time)
                                       ->subMinutes($reminderTime)
            ]);
        }

        Log::info("Attendance reminders sent for session {$session->id}");
    }

    public function sendAbsenceAlert($student, $session)
    {
        $parent = $student->parent;
        if (!$parent) return;

        $settings = $this->getUserNotificationSettings($parent, 'absence_alert');
        if (!$settings['enabled']) return;

        $message = $this->buildAbsenceAlertMessage($student, $session);
        
        $this->sendNotification([
            'user_id' => $parent->id,
            'type' => 'absence_alert',
            'title' => 'تنبيه غياب',
            'message' => $message,
            'data' => [
                'student_id' => $student->id,
                'session_id' => $session->id,
                'absence_date' => $session->session_date
            ],
            'channels' => $settings['channels'],
            'priority' => 'high'
        ]);

        // Also notify teacher if enabled
        $teacher = $session->schedule->circle->teacher;
        $teacherSettings = $this->getUserNotificationSettings($teacher, 'absence_alert');
        
        if ($teacherSettings['enabled']) {
            $teacherMessage = "الطالب {$student->name} غائب عن حصة {$session->schedule->circle->name}";
            
            $this->sendNotification([
                'user_id' => $teacher->id,
                'type' => 'absence_alert',
                'title' => 'تنبيه غياب طالب',
                'message' => $teacherMessage,
                'data' => [
                    'student_id' => $student->id,
                    'session_id' => $session->id
                ],
                'channels' => $teacherSettings['channels'],
                'priority' => 'normal'
            ]);
        }
    }

    public function sendProgressUpdate($student, $memorizationPoint)
    {
        $parent = $student->parent;
        if (!$parent) return;

        $settings = $this->getUserNotificationSettings($parent, 'progress_update');
        if (!$settings['enabled']) return;

        $message = $this->buildProgressUpdateMessage($student, $memorizationPoint);
        
        $this->sendNotification([
            'user_id' => $parent->id,
            'type' => 'progress_update',
            'title' => 'تحديث تقدم الطالب',
            'message' => $message,
            'data' => [
                'student_id' => $student->id,
                'memorization_point_id' => $memorizationPoint->id,
                'surah_name' => $memorizationPoint->surah_name,
                'points' => $memorizationPoint->points,
                'grade' => $memorizationPoint->grade
            ],
            'channels' => $settings['channels'],
            'priority' => 'normal'
        ]);
    }

    public function sendAchievementNotification($student, $achievement)
    {
        $parent = $student->parent;
        if (!$parent) return;

        $settings = $this->getUserNotificationSettings($parent, 'achievement');
        if (!$settings['enabled']) return;

        $message = $this->buildAchievementMessage($student, $achievement);
        
        $this->sendNotification([
            'user_id' => $parent->id,
            'type' => 'achievement',
            'title' => 'إنجاز جديد! 🎉',
            'message' => $message,
            'data' => [
                'student_id' => $student->id,
                'achievement_type' => $achievement['type'],
                'achievement_data' => $achievement['data']
            ],
            'channels' => $settings['channels'],
            'priority' => 'high'
        ]);
    }

    public function sendWeeklyReport($parent, $reportData)
    {
        $settings = $this->getUserNotificationSettings($parent, 'weekly_report');
        if (!$settings['enabled']) return;

        $message = $this->buildWeeklyReportMessage($reportData);
        
        $this->sendNotification([
            'user_id' => $parent->id,
            'type' => 'weekly_report',
            'title' => 'التقرير الأسبوعي',
            'message' => $message,
            'data' => $reportData,
            'channels' => $settings['channels'],
            'priority' => 'normal'
        ]);
    }

    public function sendScheduleChangeNotification($circle, $changeDetails)
    {
        $students = $circle->students()->where('is_active', true)->get();
        
        foreach ($students as $student) {
            $parent = $student->parent;
            if (!$parent) continue;

            $settings = $this->getUserNotificationSettings($parent, 'schedule_change');
            if (!$settings['enabled']) continue;

            $message = $this->buildScheduleChangeMessage($circle, $changeDetails);
            
            $this->sendNotification([
                'user_id' => $parent->id,
                'type' => 'schedule_change',
                'title' => 'تغيير في الجدول',
                'message' => $message,
                'data' => [
                    'circle_id' => $circle->id,
                    'change_details' => $changeDetails
                ],
                'channels' => $settings['channels'],
                'priority' => 'high'
            ]);
        }

        // Notify teacher
        $teacher = $circle->teacher;
        $teacherSettings = $this->getUserNotificationSettings($teacher, 'schedule_change');
        
        if ($teacherSettings['enabled']) {
            $this->sendNotification([
                'user_id' => $teacher->id,
                'type' => 'schedule_change',
                'title' => 'تغيير في جدول الحلقة',
                'message' => $message,
                'data' => [
                    'circle_id' => $circle->id,
                    'change_details' => $changeDetails
                ],
                'channels' => $teacherSettings['channels'],
                'priority' => 'high'
            ]);
        }
    }

    public function sendBulkNotification($userIds, $notificationData)
    {
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            $settings = $this->getUserNotificationSettings($user, $notificationData['type']);
            if (!$settings['enabled']) continue;

            $this->sendNotification(array_merge($notificationData, [
                'user_id' => $userId,
                'channels' => $settings['channels']
            ]));
        }
    }

    public function sendEmergencyNotification($message, $targetGroups = ['all'])
    {
        $users = $this->getUsersByGroups($targetGroups);
        
        foreach ($users as $user) {
            $this->sendNotification([
                'user_id' => $user->id,
                'type' => 'emergency',
                'title' => 'إشعار طوارئ',
                'message' => $message,
                'data' => ['emergency' => true],
                'channels' => ['sms', 'push', 'in_app'], // Force all channels for emergency
                'priority' => 'emergency'
            ]);
        }
    }

    private function sendNotification($notificationData)
    {
        // Create notification record
        $notification = Notification::create([
            'user_id' => $notificationData['user_id'],
            'type' => $notificationData['type'],
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'data' => json_encode($notificationData['data'] ?? []),
            'priority' => $this->getPriorityLevel($notificationData['priority'] ?? 'normal'),
            'scheduled_at' => $notificationData['scheduled_at'] ?? now(),
            'is_read' => false
        ]);

        // Send through specified channels
        $channels = $notificationData['channels'] ?? ['in_app'];
        
        foreach ($channels as $channel) {
            $this->sendThroughChannel($notification, $channel, $notificationData);
        }

        return $notification;
    }

    private function sendThroughChannel($notification, $channel, $data)
    {
        switch ($channel) {
            case 'sms':
                if ($this->shouldSendSMS($notification)) {
                    SendSMSNotification::dispatch($notification, $data);
                }
                break;
                
            case 'email':
                if ($this->shouldSendEmail($notification)) {
                    SendEmailNotification::dispatch($notification, $data);
                }
                break;
                
            case 'push':
                if ($this->shouldSendPush($notification)) {
                    SendPushNotification::dispatch($notification, $data);
                }
                break;
                
            case 'in_app':
                // Already stored in database
                break;
        }
    }

    private function getUserNotificationSettings($user, $type)
    {
        $setting = NotificationSetting::where('user_id', $user->id)
                                     ->where('notification_type', $type)
                                     ->first();

        if (!$setting) {
            // Return default settings
            return [
                'enabled' => true,
                'channels' => ['in_app'],
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '07:00'
            ];
        }

        return [
            'enabled' => $setting->is_enabled,
            'channels' => json_decode($setting->channels, true) ?? ['in_app'],
            'quiet_hours_start' => $setting->quiet_hours_start,
            'quiet_hours_end' => $setting->quiet_hours_end
        ];
    }

    private function shouldSendSMS($notification)
    {
        // Check quiet hours, user preferences, etc.
        return $this->isWithinAllowedHours($notification) && 
               $this->hasValidPhoneNumber($notification->user);
    }

    private function shouldSendEmail($notification)
    {
        return $this->hasValidEmail($notification->user);
    }

    private function shouldSendPush($notification)
    {
        return $this->hasValidPushToken($notification->user);
    }

    private function isWithinAllowedHours($notification)
    {
        $settings = $this->getUserNotificationSettings($notification->user, $notification->type);
        $now = Carbon::now();
        $quietStart = Carbon::createFromTimeString($settings['quiet_hours_start']);
        $quietEnd = Carbon::createFromTimeString($settings['quiet_hours_end']);

        // If it's an emergency, ignore quiet hours
        if ($notification->priority >= self::PRIORITY_LEVELS['emergency']) {
            return true;
        }

        // Check if current time is within quiet hours
        if ($quietStart->gt($quietEnd)) {
            // Quiet hours span midnight
            return !($now->gte($quietStart) || $now->lte($quietEnd));
        } else {
            // Normal quiet hours
            return !($now->gte($quietStart) && $now->lte($quietEnd));
        }
    }

    private function hasValidPhoneNumber($user)
    {
        return !empty($user->phone) && strlen($user->phone) >= 10;
    }

    private function hasValidEmail($user)
    {
        return !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL);
    }

    private function hasValidPushToken($user)
    {
        // Check if user has valid push notification token
        return !empty($user->push_token);
    }

    private function getPriorityLevel($priority)
    {
        return self::PRIORITY_LEVELS[$priority] ?? self::PRIORITY_LEVELS['normal'];
    }

    private function getUsersByGroups($groups)
    {
        $users = collect();

        foreach ($groups as $group) {
            switch ($group) {
                case 'all':
                    $users = $users->merge(User::all());
                    break;
                case 'parents':
                    $users = $users->merge(User::where('role', 'parent')->get());
                    break;
                case 'teachers':
                    $users = $users->merge(User::where('role', 'teacher')->get());
                    break;
                case 'admins':
                    $users = $users->merge(User::where('role', 'admin')->get());
                    break;
            }
        }

        return $users->unique('id');
    }

    // Message builders
    private function buildAttendanceReminderMessage($student, $session, $reminderTime)
    {
        $circle = $session->schedule->circle;
        $sessionTime = Carbon::parse($session->session_date . ' ' . $session->actual_start_time);
        
        return "تذكير: حصة {$circle->name} للطالب {$student->name} ستبدأ خلال {$reminderTime} دقيقة في {$sessionTime->format('H:i')}";
    }

    private function buildAbsenceAlertMessage($student, $session)
    {
        $circle = $session->schedule->circle;
        return "تنبيه: الطالب {$student->name} غائب عن حصة {$circle->name} اليوم {$session->session_date}";
    }

    private function buildProgressUpdateMessage($student, $memorizationPoint)
    {
        return "تحديث تقدم: الطالب {$student->name} حصل على {$memorizationPoint->points} نقطة في {$memorizationPoint->surah_name} بتقدير {$memorizationPoint->grade}/10";
    }

    private function buildAchievementMessage($student, $achievement)
    {
        switch ($achievement['type']) {
            case 'surah_completed':
                return "مبروك! الطالب {$student->name} أكمل حفظ سورة {$achievement['data']['surah_name']} 🎉";
            case 'perfect_attendance':
                return "إنجاز رائع! الطالب {$student->name} حقق حضور مثالي لمدة {$achievement['data']['period']} 👏";
            case 'high_grade':
                return "تقدير ممتاز! الطالب {$student->name} حصل على تقدير {$achievement['data']['grade']}/10 ⭐";
            default:
                return "إنجاز جديد للطالب {$student->name}!";
        }
    }

    private function buildWeeklyReportMessage($reportData)
    {
        return "التقرير الأسبوعي: حضور {$reportData['attendance_rate']}% - تقدم الحفظ {$reportData['memorization_progress']} نقطة - متوسط التقييم {$reportData['average_grade']}/10";
    }

    private function buildScheduleChangeMessage($circle, $changeDetails)
    {
        return "تغيير في جدول حلقة {$circle->name}: {$changeDetails['description']}. الجدول الجديد: {$changeDetails['new_schedule']}";
    }
}

