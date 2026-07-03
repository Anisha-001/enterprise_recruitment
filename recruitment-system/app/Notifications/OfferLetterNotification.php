<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OfferLetterNotification extends Notification
{
    use Queueable;

    public function __construct(
        public mixed $offer
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id ?? null,
            'message' => 'An offer letter has been generated for you.',
        ];
    }
}
