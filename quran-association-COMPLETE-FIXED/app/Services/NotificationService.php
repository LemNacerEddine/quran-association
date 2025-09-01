<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\MemorizationPoint;
use App\Models\Notification;
use App\Notifications\StudentProgressNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send progress notification to parent
     */
    public function sendProgressNotification(Student $student, MemorizationPoint $memorizationPoint)
    {
        try {
            // Get parent user
            $parent = User::find($student->parent_id);
            
            if (!$parent) {
                Log::warning("Parent not found for student {$student->id}");
                return false;
            }

            // Send notification
            $parent->notify(new StudentProgressNotification($student, $memorizationPoint, 'progress'));

            // Create notification record
            $this->createNotificationRecord($parent, $student, 'progress', [
                'surah_name' => $memorizationPoint->surah_name,
                'points' => $memorizationPoint->points,
            ]);

            Log::info("Progress notification sent to parent {$parent->id} for student {$student->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send progress notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send attendance notification to parent
     */
    public function sendAttendanceNotification(Student $student)
    {
        try {
            // Get parent user
            $parent = User::find($student->parent_id);
            
            if (!$parent) {
                Log::warning("Parent not found for student {$student->id}");
                return false;
            }

            // Send notification
            $parent->notify(new StudentProgressNotification($student, null, 'attendance'));

            // Create notification record
            $this->createNotificationRecord($parent, $student, 'attendance');

            Log::info("Attendance notification sent to parent {$parent->id} for student {$student->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send attendance notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send general notification to user
     */
    public function sendGeneralNotification(User $user, $title, $message, $type = 'general')
    {
        try {
            // Create notification record
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'data' => json_encode([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'created_at' => now()->toISOString(),
                ])
            ]);

            Log::info("General notification sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send general notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all parents
     */
    public function sendNotificationToAllParents($title, $message, $type = 'announcement')
    {
        try {
            $parents = User::where('role', 'parent')->get();
            $count = 0;

            foreach ($parents as $parent) {
                if ($this->sendGeneralNotification($parent, $title, $message, $type)) {
                    $count++;
                }
            }

            Log::info("Sent notification to {$count} parents");
            return $count;

        } catch (\Exception $e) {
            Log::error("Failed to send notification to all parents: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications(User $user, $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, User $user)
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $user->id)
                ->first();

            if ($notification) {
                $notification->update(['is_read' => true]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user)
    {
        try {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to mark all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Create notification record in database
     */
    private function createNotificationRecord(User $user, Student $student, $type, $extraData = [])
    {
        $title = '';
        $message = '';

        switch ($type) {
            case 'progress':
                $title = 'تقدم جديد في الحفظ';
                $message = "أحرز {$student->name} تقدماً جديداً في حفظ القرآن الكريم";
                break;
            case 'attendance':
                $title = 'تسجيل حضور';
                $message = "تم تسجيل حضور {$student->name} اليوم";
                break;
        }

        $data = array_merge([
            'student_id' => $student->id,
            'student_name' => $student->name,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'created_at' => now()->toISOString(),
        ], $extraData);

        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'data' => json_encode($data)
        ]);
    }
}

