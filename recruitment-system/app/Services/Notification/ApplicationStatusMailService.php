<?php

namespace App\Services\Notification;

use App\Models\Application;
use App\Models\EmailLog;
use App\Models\Interview;
use App\Mail\ScreeningStartedMail;
use App\Mail\ShortlistedMail;
use App\Mail\InterviewStageMail;
use App\Mail\OfferAcceptedConfirmationMail;
use App\Mail\HiredMail;
use App\Mail\RejectionMail;
use App\Mail\OnHoldMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ApplicationStatusMailService
{
    /**
     * Determine and send the correct status email to the candidate.
     */
    public function sendStatusEmail(Application $application, string $newStatus): void
    {
        $candidate = $application->candidate;
        if (!$candidate || !$candidate->email) {
            return;
        }

        // Determine if notification is enabled in config
        if (!config('recruitment.notifications.notify_candidate_on_status_change', true)) {
            Log::info('Status change notifications are disabled in configuration.', ['application_id' => $application->id]);
            return;
        }

        $mailable = $this->getMailableForStatus($application, $newStatus);

        if (!$mailable) {
            Log::info('No status email configured for status.', ['status' => $newStatus, 'application_id' => $application->id]);
            return;
        }

        try {
            // Build the mailable to get the subject
            $mailable->build();
            $subject = $mailable->subject ?? 'Application Update';
            
            // Render body for database logging
            $body = '';
            try {
                $body = $mailable->render();
            } catch (\Exception $e) {
                Log::error('Failed to render mail body for logging', ['error' => $e->getMessage()]);
            }

            // Create email log record
            $emailLog = EmailLog::create([
                'emailable_type' => Application::class,
                'emailable_id' => $application->id,
                'recipient_email' => $candidate->email,
                'recipient_name' => $candidate->full_name,
                'template' => 'status_change_' . $newStatus,
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Dispatch mail (will be queued since the Mailables implement ShouldQueue)
            Mail::to($candidate->email)->send($mailable);

            // Log activity to existing activity_log table (using Spatie Activity Log via Spatie's helper or model relation)
            activity()
                ->performedOn($application)
                ->causedBy(auth()->user())
                ->withProperties([
                    'status' => $newStatus,
                    'email_log_id' => $emailLog->id,
                ])
                ->log("Status update email sent to candidate: {$newStatus}");

            Log::info('Status-change email sent and logged.', [
                'application_id' => $application->id,
                'status' => $newStatus,
                'email_log_id' => $emailLog->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send or log status-change email', [
                'application_id' => $application->id,
                'status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Resolve the appropriate mailable class for the status.
     */
    private function getMailableForStatus(Application $application, string $status): ?object
    {
        switch ($status) {
            case 'screening':
                return new ScreeningStartedMail($application);

            case 'shortlisted':
                return new ShortlistedMail($application);

            case 'technical_interview':
            case 'manager_interview':
            case 'final_interview':
                // Fetch next scheduled/confirmed interview details
                $interview = $application->interviews()
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->orderBy('scheduled_date', 'asc')
                    ->orderBy('start_time', 'asc')
                    ->first();
                return new InterviewStageMail($application, $interview);

            case 'offer_accepted':
                return new OfferAcceptedConfirmationMail($application);

            case 'hired':
                return new HiredMail($application);

            case 'rejected':
                $reason = null;
                if (config('recruitment.notifications.show_rejection_reason_to_candidate', false)) {
                    $reason = $application->rejection_notes ?? $application->rejection_reason;
                }
                return new RejectionMail($application, $reason);

            case 'on_hold':
                return new OnHoldMail($application);

            default:
                // offer_pending, offer_sent are handled by OfferService directly
                // withdrawn doesn't send emails
                // new is handled on application submission
                return null;
        }
    }
}
