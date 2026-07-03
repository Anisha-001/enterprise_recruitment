<?php

namespace App\Events;

use App\Models\Interview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InterviewCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
        public readonly string $reason,
    ) {}
}
