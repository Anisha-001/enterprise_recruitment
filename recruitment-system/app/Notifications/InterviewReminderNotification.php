<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $interview
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'interview_id' => $this->interview->id ?? null,
            'message' => 'This is a reminder for your upcoming interview.',
        ];
    }
}
