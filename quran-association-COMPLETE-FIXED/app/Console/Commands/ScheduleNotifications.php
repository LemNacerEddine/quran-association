<?php

namespace App\Console\Commands;

use App\Jobs\SendAttendanceReminder;
use App\Jobs\SendWeeklyAttendanceReport;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleNotifications extends Command
{
    protected $signature = 'notifications:schedule';
    protected $description = 'Schedule automatic notifications for attendance reminders and reports';

    public function handle()
    {
        $this->info('Starting notification scheduling...');

        // Schedule attendance reminders for tomorrow's sessions
        $this->scheduleAttendanceReminders();

        // Schedule weekly reports (run on Sundays)
        $this->scheduleWeeklyReports();

        $this->info('Notification scheduling completed.');
    }

    private function scheduleAttendanceReminders(): void
    {
        $tomorrow = Carbon::tomorrow();
        
        $sessions = ClassSession::where('session_date', $tomorrow)
                               ->where('status', 'scheduled')
                               ->get();

        foreach ($sessions as $session) {
            // Schedule reminder 1 hour before session
            $sessionDateTime = Carbon::parse($session->session_date)
                                   ->setTimeFromTimeString($session->actual_start_time ?? '08:00');
            
            $reminderTime = $sessionDateTime->subHour();

            if ($reminderTime->isFuture()) {
                SendAttendanceReminder::dispatch($session)->delay($reminderTime);
                $this->info("Scheduled attendance reminder for session: {$session->session_title}");
            }
        }
    }

    private function scheduleWeeklyReports(): void
    {
        // Only run on Sundays
        if (Carbon::now()->dayOfWeek === Carbon::SUNDAY) {
            // Schedule for 8 PM
            $reportTime = Carbon::now()->setTime(20, 0);
            
            if ($reportTime->isFuture()) {
                SendWeeklyAttendanceReport::dispatch()->delay($reportTime);
                $this->info("Scheduled weekly attendance reports for {$reportTime->format('Y-m-d H:i')}");
            } else {
                // If it's past 8 PM, send immediately
                SendWeeklyAttendanceReport::dispatch();
                $this->info("Dispatched weekly attendance reports immediately");
            }
        }
    }
}

