<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class AdvancedNotificationService
{
    /**
     * Send achievement notification to parent
     */
    public function sendAchievementNotification(Student $student, string $achievement, array $data = []): void
    {
        if (!$student->parent) return;

        $settings = $this->getNotificationSettings($student->parent, 'achievement');
        if (!$settings->is_enabled) return;

        Notification::create([
            'user_id' => $student->parent->id,
            'title' => 'إنجاز جديد',
            'message' => "تهانينا! حقق الطالب {$student->name} إنجازاً جديداً: {$achievement}",
            'type' => 'achievement',
            'data' => array_merge([
                'student_id' => $student->id,
                'achievement' => $achievement
            ], $data)
        ]);
    }

    /**
     * Send behavior notification to parent
     */
    public function sendBehaviorNotification(Student $student, string $behavior, string $type = 'positive'): void
    {
        if (!$student->parent) return;

        $settings = $this->getNotificationSettings($student->parent, 'behavior');
        if (!$settings->is_enabled) return;

        $title = $type === 'positive' ? 'سلوك إيجابي' : 'ملاحظة سلوكية';
        $icon = $type === 'positive' ? '👍' : '⚠️';

        Notification::create([
            'user_id' => $student->parent->id,
            'title' => $title,
            'message' => "{$icon} {$behavior} - الطالب: {$student->name}",
            'type' => 'behavior',
            'data' => [
                'student_id' => $student->id,
                'behavior' => $behavior,
                'behavior_type' => $type
            ]
        ]);
    }

    /**
     * Send homework notification to parent
     */
    public function sendHomeworkNotification(Student $student, string $homework, Carbon $dueDate): void
    {
        if (!$student->parent) return;

        $settings = $this->getNotificationSettings($student->parent, 'homework');
        if (!$settings->is_enabled) return;

        Notification::create([
            'user_id' => $student->parent->id,
            'title' => 'واجب منزلي جديد',
            'message' => "تم تكليف الطالب {$student->name} بواجب منزلي جديد: {$homework}\nموعد التسليم: {$dueDate->format('Y-m-d')}",
            'type' => 'homework',
            'data' => [
                'student_id' => $student->id,
                'homework' => $homework,
                'due_date' => $dueDate->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Send session cancellation notification
     */
    public function sendSessionCancellationNotification(ClassSession $session, string $reason): void
    {
        $parents = User::where('role', 'parent')
                      ->whereHas('children', function($query) use ($session) {
                          $query->where('circle_id', $session->circle_id);
                      })
                      ->get();

        foreach ($parents as $parent) {
            $settings = $this->getNotificationSettings($parent, 'session_update');
            if (!$settings->is_enabled) continue;

            Notification::create([
                'user_id' => $parent->id,
                'title' => 'إلغاء الحصة',
                'message' => "تم إلغاء حصة {$session->session_title} المقررة في {$session->session_date->format('Y-m-d')}\nالسبب: {$reason}",
                'type' => 'session_cancellation',
                'data' => [
                    'session_id' => $session->id,
                    'session_title' => $session->session_title,
                    'session_date' => $session->session_date->format('Y-m-d'),
                    'reason' => $reason
                ]
            ]);
        }
    }

    /**
     * Send session reschedule notification
     */
    public function sendSessionRescheduleNotification(ClassSession $session, Carbon $newDate, string $newTime): void
    {
        $parents = User::where('role', 'parent')
                      ->whereHas('children', function($query) use ($session) {
                          $query->where('circle_id', $session->circle_id);
                      })
                      ->get();

        foreach ($parents as $parent) {
            $settings = $this->getNotificationSettings($parent, 'session_update');
            if (!$settings->is_enabled) continue;

            Notification::create([
                'user_id' => $parent->id,
                'title' => 'تغيير موعد الحصة',
                'message' => "تم تغيير موعد حصة {$session->session_title}\nالموعد الجديد: {$newDate->format('Y-m-d')} الساعة {$newTime}",
                'type' => 'session_reschedule',
                'data' => [
                    'session_id' => $session->id,
                    'session_title' => $session->session_title,
                    'old_date' => $session->session_date->format('Y-m-d'),
                    'new_date' => $newDate->format('Y-m-d'),
                    'new_time' => $newTime
                ]
            ]);
        }
    }

    /**
     * Send monthly progress report
     */
    public function sendMonthlyProgressReport(Student $student): void
    {
        if (!$student->parent) return;

        $settings = $this->getNotificationSettings($student->parent, 'monthly_report');
        if (!$settings->is_enabled) return;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $progressData = $this->calculateMonthlyProgress($student, $startOfMonth, $endOfMonth);

        Notification::create([
            'user_id' => $student->parent->id,
            'title' => 'التقرير الشهري',
            'message' => $this->formatMonthlyReport($student, $progressData),
            'type' => 'monthly_report',
            'data' => [
                'student_id' => $student->id,
                'month' => $startOfMonth->format('Y-m'),
                'progress_data' => $progressData
            ]
        ]);
    }

    /**
     * Send absence alert for consecutive absences
     */
    public function checkAndSendAbsenceAlert(Student $student): void
    {
        if (!$student->parent) return;

        $settings = $this->getNotificationSettings($student->parent, 'absence_alert');
        if (!$settings->is_enabled) return;

        // Check for consecutive absences in the last 7 days
        $recentAbsences = AttendanceSession::where('student_id', $student->id)
                                          ->where('status', 'absent')
                                          ->whereHas('session', function($query) {
                                              $query->where('session_date', '>=', Carbon::now()->subDays(7));
                                          })
                                          ->count();

        if ($recentAbsences >= 3) {
            Notification::create([
                'user_id' => $student->parent->id,
                'title' => 'تنبيه غياب متكرر',
                'message' => "تنبيه: الطالب {$student->name} غاب {$recentAbsences} مرات في الأسبوع الماضي. يرجى المتابعة مع المعلم.",
                'type' => 'absence_alert',
                'data' => [
                    'student_id' => $student->id,
                    'absence_count' => $recentAbsences,
                    'period' => '7 days'
                ]
            ]);
        }
    }

    /**
     * Get notification settings for user and type
     */
    private function getNotificationSettings(User $user, string $type): NotificationSetting
    {
        return $user->notificationSettings()
                   ->where('type', $type)
                   ->firstOrCreate([
                       'type' => $type
                   ], [
                       'is_enabled' => true,
                       'delivery_method' => 'app',
                       'settings' => []
                   ]);
    }

    /**
     * Calculate monthly progress for student
     */
    private function calculateMonthlyProgress(Student $student, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = AttendanceSession::where('student_id', $student->id)
                                       ->whereHas('session', function($query) use ($startDate, $endDate) {
                                           $query->whereBetween('session_date', [$startDate, $endDate]);
                                       })
                                       ->with('session')
                                       ->get();

        $totalSessions = $attendances->count();
        $presentSessions = $attendances->whereIn('status', ['present', 'late'])->count();
        $attendancePercentage = $totalSessions > 0 ? 
            round(($presentSessions / $totalSessions) * 100, 1) : 0;

        $averageParticipation = $attendances->where('participation_score', '>', 0)
                                          ->avg('participation_score');

        // Get memorization points for the month
        $memorizationPoints = $student->memorizationPoints()
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->sum('points');

        return [
            'total_sessions' => $totalSessions,
            'present_sessions' => $presentSessions,
            'attendance_percentage' => $attendancePercentage,
            'average_participation' => round($averageParticipation, 1),
            'memorization_points' => $memorizationPoints,
            'month_name' => $startDate->format('F Y')
        ];
    }

    /**
     * Format monthly report message
     */
    private function formatMonthlyReport(Student $student, array $data): string
    {
        $message = "التقرير الشهري للطالب {$student->name} - {$data['month_name']}\n\n";
        $message .= "📊 إحصائيات الحضور:\n";
        $message .= "• إجمالي الجلسات: {$data['total_sessions']}\n";
        $message .= "• جلسات الحضور: {$data['present_sessions']}\n";
        $message .= "• نسبة الحضور: {$data['attendance_percentage']}%\n\n";
        
        if ($data['average_participation'] > 0) {
            $message .= "🎯 متوسط المشاركة: {$data['average_participation']}/10\n\n";
        }
        
        $message .= "⭐ نقاط الحفظ: {$data['memorization_points']} نقطة\n\n";
        
        // Add performance assessment
        if ($data['attendance_percentage'] >= 90) {
            $message .= "🏆 أداء ممتاز! استمر في التفوق";
        } elseif ($data['attendance_percentage'] >= 75) {
            $message .= "👍 أداء جيد، يمكن تحسينه أكثر";
        } else {
            $message .= "⚠️ يحتاج إلى تحسين في الحضور";
        }

        return $message;
    }
}

