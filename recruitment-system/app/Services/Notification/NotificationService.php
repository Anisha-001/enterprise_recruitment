<?php

namespace App\Services\Notification;

use App\Models\Application;
use App\Models\Interview;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\ApplicationStatusChangedNotification;
use App\Notifications\InterviewInvitationNotification;
use App\Notifications\InterviewRescheduledNotification;
use App\Notifications\InterviewCancellationNotification;
use App\Notifications\InterviewReminderNotification;
use App\Notifications\OfferLetterNotification;
use App\Notifications\OfferAcceptedNotification;
use App\Notifications\OfferRejectedNotification;
use App\Notifications\NewApplicationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendStatusChangeNotification(Application $application, string $oldStatus, string $newStatus): void
    {
        try {
            $candidate = $application->candidate;

            $notificationMapping = [
                'screening' => ['mail' => 'application_under_review'],
                'shortlisted' => ['mail' => 'application_shortlisted'],
                'offer_sent' => ['mail' => 'offer_letter'],
                'hired' => ['mail' => 'welcome_aboard'],
                'rejected' => ['mail' => 'rejection'],
            ];

            if (isset($notificationMapping[$newStatus])) {
                $candidate->notify(new ApplicationStatusChangedNotification($application, $oldStatus, $newStatus));
            }

            // Notify recruiter
            if ($application->recruiter) {
                $application->recruiter->notify(new NewApplicationNotification($application, "Status changed: {$oldStatus} → {$newStatus}"));
            }

            // Notify hiring manager
            if ($application->jobPosting->hiring_manager && $application->jobPosting->hiring_manager_id !== $application->recruiter_id) {
                $application->jobPosting->hiring_manager->notify(new NewApplicationNotification($application, "Status changed: {$oldStatus} → {$newStatus}"));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status change notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendInterviewInvitation(Interview $interview): void
    {
        try {
            $candidate = $interview->candidate;
            $candidate->notify(new InterviewInvitationNotification($interview));

            foreach ($interview->interviewers as $interviewer) {
                $interviewer->notify(new InterviewInvitationNotification($interview, true));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send interview invitation', [
                'interview_id' => $interview->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendInterviewRescheduled(Interview $interview): void
    {
        try {
            $candidate = $interview->candidate;
            $candidate->notify(new InterviewRescheduledNotification($interview));

            foreach ($interview->interviewers as $interviewer) {
                $interviewer->notify(new InterviewRescheduledNotification($interview, true));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send interview reschedule notification', [
                'interview_id' => $interview->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendInterviewCancellation(Interview $interview, string $reason): void
    {
        try {
            $candidate = $interview->candidate;
            $candidate->notify(new InterviewCancellationNotification($interview, $reason));

            foreach ($interview->interviewers as $interviewer) {
                $interviewer->notify(new InterviewCancellationNotification($interview, $reason, true));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send interview cancellation notification', [
                'interview_id' => $interview->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendInterviewReminder(Interview $interview): void
    {
        try {
            $candidate = $interview->candidate;
            $candidate->notify(new InterviewReminderNotification($interview));
        } catch (\Exception $e) {
            Log::error('Failed to send interview reminder', [
                'interview_id' => $interview->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOfferLetter(Offer $offer): void
    {
        try {
            $candidate = $offer->candidate;
            $candidate->notify(new OfferLetterNotification($offer));
        } catch (\Exception $e) {
            Log::error('Failed to send offer letter', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOfferAcceptedNotification(Offer $offer): void
    {
        try {
            $recruiter = $offer->application->recruiter;
            $hiringManager = $offer->jobPosting->hiring_manager;

            if ($recruiter) {
                $recruiter->notify(new OfferAcceptedNotification($offer));
            }

            if ($hiringManager && $hiringManager->id !== $recruiter?->id) {
                $hiringManager->notify(new OfferAcceptedNotification($offer));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send offer accepted notification', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOfferRejectedNotification(Offer $offer, ?string $reason): void
    {
        try {
            $recruiter = $offer->application->recruiter;
            $hiringManager = $offer->jobPosting->hiring_manager;

            if ($recruiter) {
                $recruiter->notify(new OfferRejectedNotification($offer, $reason));
            }

            if ($hiringManager && $hiringManager->id !== $recruiter?->id) {
                $hiringManager->notify(new OfferRejectedNotification($offer, $reason));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send offer rejected notification', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function notifyNewApplication(Application $application): void
    {
        try {
            $recruiter = $application->recruiter;
            $hiringManager = $application->jobPosting->hiring_manager;

            if ($recruiter) {
                $recruiter->notify(new NewApplicationNotification($application));
            }

            if ($hiringManager && $hiringManager->id !== $recruiter?->id) {
                $hiringManager->notify(new NewApplicationNotification($application));
            }

            // Notify HR admin users
            $hrAdmins = User::role('hr_admin')->get();
            Notification::send($hrAdmins, new NewApplicationNotification($application));
        } catch (\Exception $e) {
            Log::error('Failed to send new application notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
