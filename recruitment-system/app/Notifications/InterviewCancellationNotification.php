<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewCancellationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $interview,
        public string $reason,
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
            'reason' => $this->reason,
            'is_interviewer' => $this->isInterviewer,
            'message' => "The interview has been cancelled. Reason: {$this->reason}",
        ];
    }
}
