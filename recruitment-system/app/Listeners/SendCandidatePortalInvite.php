<?php

namespace App\Listeners;

use App\Events\ApplicationSubmitted;
use App\Notifications\CandidateInviteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SendCandidatePortalInvite implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ApplicationSubmitted $event): void
    {
        $application = $event->application;
        $candidate = $application->candidate;

        if (!$candidate) {
            return;
        }

        // If the candidate has no password set, invite them to set one
        if (empty($candidate->password)) {
            Log::info('Candidate has no password set, generating invite.', ['candidate_id' => $candidate->id]);

            // Generate signed route valid for 24 hours
            $signedUrl = URL::temporarySignedRoute(
                'candidate.set-password',
                now()->addHours(24),
                ['email' => $candidate->email]
            );

            // Send invite notification
            $candidate->notify(new CandidateInviteNotification($application, $signedUrl));
        } else {
            Log::info('Candidate already has a password set. Skipping invite.', ['candidate_id' => $candidate->id]);
        }
    }
}
