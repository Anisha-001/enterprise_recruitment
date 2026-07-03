<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewStageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Application $application,
        public readonly ?Interview $interview = null
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Application Update: Interview Round Scheduled')
            ->view('emails.status.interview')
            ->with([
                'interview' => $this->interview,
            ]);
    }
}
