<?php

namespace App\Listeners;

use App\Events\ApplicationStatusChanged;
use App\Services\Notification\ApplicationStatusMailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendApplicationStatusEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ApplicationStatusMailService $mailService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ApplicationStatusChanged $event): void
    {
        $application = $event->application;
        $newStatus = $event->newStatus;

        Log::info('Handling ApplicationStatusChanged event for mailing.', [
            'application_id' => $application->id,
            'new_status' => $newStatus,
        ]);

        // Send status email using service
        $this->mailService->sendStatusEmail($application, $newStatus);
    }
}
