<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Student;
use App\Models\MemorizationPoint;

class StudentProgressNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $memorizationPoint;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, MemorizationPoint $memorizationPoint = null, $type = 'progress')
    {
        $this->student = $student;
        $this->memorizationPoint = $memorizationPoint;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('تحديث حول تقدم طفلك في تحفيظ القرآن الكريم')
            ->greeting('السلام عليكم ورحمة الله وبركاته')
            ->line('نود إعلامكم بتحديث جديد حول تقدم طفلكم في تحفيظ القرآن الكريم.');

        if ($this->type === 'progress' && $this->memorizationPoint) {
            $message->line("الطالب: {$this->student->name}")
                   ->line("السورة: {$this->memorizationPoint->surah_name}")
                   ->line("من الآية: {$this->memorizationPoint->from_verse} إلى الآية: {$this->memorizationPoint->to_verse}")
                   ->line("النقاط المكتسبة: {$this->memorizationPoint->points}")
                   ->line("التاريخ: {$this->memorizationPoint->created_at->format('Y-m-d')}");
        } elseif ($this->type === 'attendance') {
            $message->line("تم تسجيل حضور الطالب: {$this->student->name}")
                   ->line("التاريخ: " . now()->format('Y-m-d'));
        }

        return $message->line('نشكركم على متابعتكم المستمرة لتقدم طفلكم.')
                      ->line('بارك الله فيكم وفي أطفالكم.')
                      ->salutation('جمعية تحفيظ القرآن الكريم');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'type' => $this->type,
            'created_at' => now()->toISOString(),
        ];

        if ($this->type === 'progress' && $this->memorizationPoint) {
            $data['memorization'] = [
                'surah_name' => $this->memorizationPoint->surah_name,
                'from_verse' => $this->memorizationPoint->from_verse,
                'to_verse' => $this->memorizationPoint->to_verse,
                'points' => $this->memorizationPoint->points,
            ];
            $data['title'] = 'تقدم جديد في الحفظ';
            $data['message'] = "أحرز {$this->student->name} تقدماً جديداً في حفظ سورة {$this->memorizationPoint->surah_name}";
        } elseif ($this->type === 'attendance') {
            $data['title'] = 'تسجيل حضور';
            $data['message'] = "تم تسجيل حضور {$this->student->name} اليوم";
        }

        return $data;
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'student_progress';
    }
}

