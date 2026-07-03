<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferAcceptedConfirmationMail extends Mailable implements ShouldQueue
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
        return $this->subject('Offer Accepted Confirmation - Welcome Aboard!')
            ->view('emails.status.offer_accepted');
    }
}
