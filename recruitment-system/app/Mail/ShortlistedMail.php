<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShortlistedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Application $application
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject("Application Update: You've been Shortlisted!")
            ->view('emails.status.shortlisted');
    }
}
