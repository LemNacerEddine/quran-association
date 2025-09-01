<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\Attendance;
use Carbon\Carbon;

class SessionStatusService
{
    /**
     * تحديث حالة جميع الجلسات بناءً على التاريخ وتسجيل الحضور
     */
    public static function updateAllSessionsStatus()
    {
        $sessions = ClassSession::all();
        $updatedCount = 0;

        foreach ($sessions as $session) {
            $oldStatus = $session->status;
            $newStatus = self::calculateSessionStatus($session);
            
            if ($oldStatus !== $newStatus) {
                $session->status = $newStatus;
                $session->save();
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * حساب حالة الجلسة بناءً على التاريخ وتسجيل الحضور
     */
    public static function calculateSessionStatus(ClassSession $session): string
    {
        $sessionDate = Carbon::parse($session->session_date);
        $today = Carbon::today();
        
        // فحص إذا كان هناك سجلات حضور مسجلة فعلياً
        $hasAttendanceRecords = Attendance::where('session_id', $session->id)->exists();
        
        if ($sessionDate->isFuture()) {
            // الجلسات المستقبلية
            return 'scheduled';
        } elseif ($sessionDate->isToday()) {
            // جلسات اليوم
            if ($hasAttendanceRecords && $session->attendance_taken) {
                return 'completed';
            } else {
                return 'ongoing'; // أو scheduled حسب الوقت
            }
        } else {
            // الجلسات الماضية
            if ($hasAttendanceRecords && $session->attendance_taken) {
                return 'completed';
            } else {
                return 'missed'; // فائتة - لم يتم تسجيل الحضور
            }
        }
    }

    /**
     * تحديث إحصائيات الحضور للجلسة
     */
    public static function updateSessionAttendanceStats(ClassSession $session)
    {
        $attendances = Attendance::where('session_id', $session->id)->get();
        
        if ($attendances->isEmpty()) {
            $session->total_students = 0;
            $session->present_students = 0;
            $session->absent_students = 0;
            $session->attendance_percentage = 0;
            $session->attendance_taken = false;
        } else {
            $session->total_students = $attendances->count();
            $session->present_students = $attendances->where('status', 'present')->count();
            $session->absent_students = $attendances->where('status', 'absent')->count();
            
            if ($session->total_students > 0) {
                $session->attendance_percentage = ($session->present_students / $session->total_students) * 100;
            }
            
            $session->attendance_taken = true;
            $session->attendance_taken_at = now();
        }
        
        $session->save();
    }

    /**
     * تحديث حالة الجلسة عند تسجيل الحضور
     */
    public static function markSessionAttendanceTaken(ClassSession $session, $userId = null)
    {
        // تحديث إحصائيات الحضور
        self::updateSessionAttendanceStats($session);
        
        // تحديث معلومات تسجيل الحضور
        $session->attendance_taken = true;
        $session->attendance_taken_at = now();
        $session->attendance_taken_by = $userId ?? auth()->id();
        
        // تحديث حالة الجلسة
        $session->status = self::calculateSessionStatus($session);
        
        $session->save();
    }

    /**
     * الحصول على إحصائيات الجلسات
     */
    public static function getSessionsStatistics(): array
    {
        $today = Carbon::today();
        
        return [
            'total_sessions' => ClassSession::count(),
            'completed_sessions' => ClassSession::where('status', 'completed')->count(),
            'missed_sessions' => ClassSession::where('status', 'missed')->count(),
            'scheduled_sessions' => ClassSession::where('status', 'scheduled')->count(),
            'ongoing_sessions' => ClassSession::where('status', 'ongoing')->count(),
            'today_sessions' => ClassSession::whereDate('session_date', $today)->count(),
            'future_sessions' => ClassSession::whereDate('session_date', '>', $today)->count(),
            'past_sessions' => ClassSession::whereDate('session_date', '<', $today)->count(),
        ];
    }

    /**
     * الحصول على الجلسات حسب الحالة
     */
    public static function getSessionsByStatus(string $status)
    {
        return ClassSession::where('status', $status)
            ->with(['circle', 'teacher'])
            ->orderBy('session_date', 'desc')
            ->get();
    }

    /**
     * الحصول على الجلسات الفائتة (التي تحتاج تسجيل حضور)
     */
    public static function getMissedSessions()
    {
        $today = Carbon::today();
        
        return ClassSession::whereDate('session_date', '<', $today)
            ->where(function($query) {
                $query->where('attendance_taken', false)
                      ->orWhere('status', 'scheduled');
            })
            ->with(['circle', 'teacher'])
            ->orderBy('session_date', 'desc')
            ->get();
    }

    /**
     * الحصول على جلسات اليوم
     */
    public static function getTodaySessions()
    {
        $today = Carbon::today();
        
        return ClassSession::whereDate('session_date', $today)
            ->with(['circle', 'teacher'])
            ->orderBy('actual_start_time')
            ->get();
    }

    /**
     * الحصول على الجلسات القادمة
     */
    public static function getUpcomingSessions($limit = 10)
    {
        $today = Carbon::today();
        
        return ClassSession::whereDate('session_date', '>', $today)
            ->with(['circle', 'teacher'])
            ->orderBy('session_date')
            ->limit($limit)
            ->get();
    }
}

