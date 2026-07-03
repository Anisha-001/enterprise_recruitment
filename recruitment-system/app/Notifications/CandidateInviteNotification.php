<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CandidateInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Application $application,
        public readonly string $signedUrl
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Set Up Your Candidate Portal Account';

        // Log the email in email_logs table
        try {
            EmailLog::create([
                'emailable_type' => Application::class,
                'emailable_id' => $this->application->id,
                'recipient_email' => $notifiable->email,
                'recipient_name' => $notifiable->full_name,
                'template' => 'candidate_portal_invite',
                'subject' => $subject,
                'body' => 'Invitation link to set password: ' . $this->signedUrl,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log candidate portal invite to email_logs', [
                'error' => $e->getMessage(),
                'candidate_id' => $notifiable->id
            ]);
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line('Thank you for applying for the ' . $this->application->jobPosting->title . ' role at ' . config('recruitment.seo.company_name') . '.')
            ->line('To complete your profile and track your application status in real-time, please set up a password for your account using the link below:')
            ->action('Set Password & Access Portal', $this->signedUrl)
            ->line('Please note that this link is secure and will expire in 24 hours.')
            ->line('If you did not make this application, please disregard this email.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'message' => 'Account setup invitation sent.',
        ];
    }
}
