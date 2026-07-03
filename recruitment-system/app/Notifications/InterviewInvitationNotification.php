<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $interview,
        public bool $isInterviewer = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'interview_id' => $this->interview->id ?? null,
            'is_interviewer' => $this->isInterviewer,
            'message' => $this->isInterviewer ? 'You have been invited to conduct an interview.' : 'You have been invited to an interview.',
        ];
    }
}
