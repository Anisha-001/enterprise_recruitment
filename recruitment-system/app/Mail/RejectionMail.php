<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Application $application,
        public readonly ?string $rejectionReason = null
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Application Update: ' . $this->application->jobPosting->title)
            ->view('emails.status.rejected')
            ->with([
                'rejectionReason' => $this->rejectionReason,
            ]);
    }
}
