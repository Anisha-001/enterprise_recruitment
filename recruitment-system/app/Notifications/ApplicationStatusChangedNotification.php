<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $application,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id ?? null,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Application status changed from {$this->oldStatus} to {$this->newStatus}.",
        ];
    }
}
