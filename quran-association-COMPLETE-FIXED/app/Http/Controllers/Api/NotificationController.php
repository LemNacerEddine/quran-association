<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 20);
        $status = $request->get('status'); // pending, sent, delivered, read, failed

        $query = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $notifications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        
        $unreadCount = Notification::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'sent', 'delivered'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();
        
        // التحقق من أن الإشعار ينتمي للمستخدم
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لهذا الإشعار'
            ], 403);
        }

        $notification->update([
            'status' => 'read',
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        $updatedCount = Notification::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'sent', 'delivered'])
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد جميع الإشعارات كمقروءة',
            'data' => [
                'updated_count' => $updatedCount
            ]
        ]);
    }

    /**
     * Get notification settings
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();
        
        $settings = NotificationSetting::where('user_id', $user->id)->get();
        
        // إعدادات افتراضية إذا لم توجد
        if ($settings->isEmpty()) {
            $defaultSettings = [
                [
                    'user_id' => $user->id,
                    'type' => 'attendance',
                    'enabled' => true,
                    'push_enabled' => true,
                    'email_enabled' => false,
                    'sms_enabled' => false,
                ],
                [
                    'user_id' => $user->id,
                    'type' => 'memorization',
                    'enabled' => true,
                    'push_enabled' => true,
                    'email_enabled' => false,
                    'sms_enabled' => false,
                ],
                [
                    'user_id' => $user->id,
                    'type' => 'report',
                    'enabled' => true,
                    'push_enabled' => true,
                    'email_enabled' => true,
                    'sms_enabled' => false,
                ],
                [
                    'user_id' => $user->id,
                    'type' => 'reminder',
                    'enabled' => true,
                    'push_enabled' => true,
                    'email_enabled' => false,
                    'sms_enabled' => false,
                ],
                [
                    'user_id' => $user->id,
                    'type' => 'announcement',
                    'enabled' => true,
                    'push_enabled' => true,
                    'email_enabled' => true,
                    'sms_enabled' => false,
                ],
            ];

            foreach ($defaultSettings as $setting) {
                NotificationSetting::create($setting);
            }

            $settings = NotificationSetting::where('user_id', $user->id)->get();
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.type' => 'required|in:attendance,memorization,report,reminder,announcement',
            'settings.*.enabled' => 'required|boolean',
            'settings.*.push_enabled' => 'required|boolean',
            'settings.*.email_enabled' => 'required|boolean',
            'settings.*.sms_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->settings as $settingData) {
            NotificationSetting::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $settingData['type']
                ],
                [
                    'enabled' => $settingData['enabled'],
                    'push_enabled' => $settingData['push_enabled'],
                    'email_enabled' => $settingData['email_enabled'],
                    'sms_enabled' => $settingData['sms_enabled'],
                ]
            );
        }

        $updatedSettings = NotificationSetting::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إعدادات الإشعارات بنجاح',
            'data' => $updatedSettings
        ]);
    }

    /**
     * Create notification (Admin only)
     */
    public function createNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:attendance,memorization,report,reminder,announcement',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'priority' => 'sometimes|in:low,normal,high',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = Notification::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'title' => $request->title,
            'body' => $request->body,
            'data' => $request->data,
            'priority' => $request->get('priority', 'normal'),
            'scheduled_at' => $request->scheduled_at,
            'expires_at' => $request->expires_at,
            'status' => $request->scheduled_at ? 'pending' : 'sent',
            'sent_at' => $request->scheduled_at ? null : now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الإشعار بنجاح',
            'data' => $notification
        ], 201);
    }

    /**
     * Send bulk notifications (Admin only)
     */
    public function sendBulkNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'type' => 'required|in:attendance,memorization,report,reminder,announcement',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'priority' => 'sometimes|in:low,normal,high',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $notifications = [];
        foreach ($request->user_ids as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $request->type,
                'title' => $request->title,
                'body' => $request->body,
                'data' => json_encode($request->data),
                'priority' => $request->get('priority', 'normal'),
                'scheduled_at' => $request->scheduled_at,
                'expires_at' => $request->expires_at,
                'status' => $request->scheduled_at ? 'pending' : 'sent',
                'sent_at' => $request->scheduled_at ? null : now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الإشعارات بنجاح',
            'data' => [
                'sent_count' => count($notifications)
            ]
        ], 201);
    }
}

