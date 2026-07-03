<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'emailable_type',
        'emailable_id',
        'recipient_email',
        'recipient_name',
        'template',
        'subject',
        'body',
        'status',
        'error_message',
        'message_id',
        'sent_at',
        'delivered_at',
        'opened_at',
        'ip_address',
        'meta',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'meta' => 'array',
    ];

    public function emailable()
    {
        return $this->morphTo();
    }

    public function markAsSent(): void
    {
        $this->update(['status' => 'sent', 'sent_at' => now()]);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => 'delivered', 'delivered_at' => now()]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update(['status' => 'failed', 'error_message' => $error]);
    }

    public function markAsOpened(): void
    {
        if (!$this->opened_at) {
            $this->update(['status' => 'opened', 'opened_at' => now()]);
        }
    }

    public const STATUSES = [
        'queued' => 'Queued',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
        'bounced' => 'Bounced',
        'opened' => 'Opened',
        'clicked' => 'Clicked',
    ];

    public const TEMPLATES = [
        'application_received' => 'Application Received',
        'application_under_review' => 'Application Under Review',
        'interview_invitation' => 'Interview Invitation',
        'interview_reminder' => 'Interview Reminder',
        'interview_rescheduled' => 'Interview Rescheduled',
        'interview_cancelled' => 'Interview Cancelled',
        'offer_letter' => 'Offer Letter',
        'offer_reminder' => 'Offer Reminder',
        'rejection' => 'Rejection',
        'feedback_request' => 'Feedback Request',
        'welcome_aboard' => 'Welcome Aboard',
    ];
}
