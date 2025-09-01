<?php

namespace App\Jobs;

use App\Models\AttendanceSession;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SendWeeklyAttendanceReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Get all parents
        $parents = User::where('role', 'parent')->with('children')->get();

        foreach ($parents as $parent) {
            // Check if parent wants weekly reports
            $settings = $parent->notificationSettings()
                              ->where('notification_type', 'report')
                              ->first();

            if (!$settings || !$settings->is_enabled) {
                continue;
            }

            foreach ($parent->children as $student) {
                $attendanceData = $this->getWeeklyAttendanceData($student, $startOfWeek, $endOfWeek);
                
                if ($attendanceData['total_sessions'] > 0) {
                    $this->sendWeeklyReport($parent, $student, $attendanceData, $startOfWeek, $endOfWeek);
                }
            }
        }
    }

    private function getWeeklyAttendanceData(Student $student, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = AttendanceSession::where('student_id', $student->id)
                                       ->whereHas('session', function($query) use ($startDate, $endDate) {
                                           $query->whereBetween('session_date', [$startDate, $endDate]);
                                       })
                                       ->with('session')
                                       ->get();

        $totalSessions = $attendances->count();
        $presentSessions = $attendances->whereIn('status', ['present', 'late'])->count();
        $absentSessions = $attendances->where('status', 'absent')->count();
        $excusedSessions = $attendances->where('status', 'excused')->count();
        
        $attendancePercentage = $totalSessions > 0 ? 
            round(($presentSessions / $totalSessions) * 100, 1) : 0;

        $averageParticipation = $attendances->where('participation_score', '>', 0)
                                          ->avg('participation_score');

        return [
            'total_sessions' => $totalSessions,
            'present_sessions' => $presentSessions,
            'absent_sessions' => $absentSessions,
            'excused_sessions' => $excusedSessions,
            'attendance_percentage' => $attendancePercentage,
            'average_participation' => round($averageParticipation, 1),
            'attendances' => $attendances
        ];
    }

    private function sendWeeklyReport(User $parent, Student $student, array $data, Carbon $startDate, Carbon $endDate): void
    {
        $message = "تقرير الحضور الأسبوعي للطالب {$student->name}\n";
        $message .= "الفترة: {$startDate->format('Y-m-d')} إلى {$endDate->format('Y-m-d')}\n\n";
        $message .= "إجمالي الجلسات: {$data['total_sessions']}\n";
        $message .= "الحضور: {$data['present_sessions']}\n";
        $message .= "الغياب: {$data['absent_sessions']}\n";
        $message .= "الغياب المبرر: {$data['excused_sessions']}\n";
        $message .= "نسبة الحضور: {$data['attendance_percentage']}%\n";
        
        if ($data['average_participation'] > 0) {
            $message .= "متوسط المشاركة: {$data['average_participation']}/10\n";
        }

        Notification::create([
            'user_id' => $parent->id,
            'title' => 'تقرير الحضور الأسبوعي',
            'message' => $message,
            'type' => 'weekly_report',
            'data' => [
                'student_id' => $student->id,
                'week_start' => $startDate->format('Y-m-d'),
                'week_end' => $endDate->format('Y-m-d'),
                'attendance_data' => $data
            ]
        ]);
    }
}

