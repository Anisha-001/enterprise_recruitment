<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewApplicationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $application,
        public ?string $customMessage = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id ?? null,
            'message' => $this->customMessage ?? 'A new application has been submitted.',
        ];
    }
}
