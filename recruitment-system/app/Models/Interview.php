<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Interview extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'application_id',
        'candidate_id',
        'job_posting_id',
        'round_type',
        'round_number',
        'mode',
        'video_platform',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'scheduled_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'location_id',
        'interview_address',
        'room_number',
        'instructions',
        'description',
        'status',
        'reminder_sent_at',
        'follow_up_sent_at',
        'scheduled_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'reminder_sent_at' => 'datetime',
        'follow_up_sent_at' => 'datetime',
        'duration_minutes' => 'integer',
        'round_number' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($interview) {
            if ($interview->application_id) {
                $application = $interview->application;
                if ($application) {
                    $interview->candidate_id = $application->candidate_id;
                    $interview->job_posting_id = $application->job_posting_id;
                    
                    if (empty($interview->round_number)) {
                        $interview->round_number = $application->interviews()->max('round_number') + 1;
                    }
                }
            }
        });

        static::creating(function ($interview) {
            if (auth()->check() && empty($interview->scheduled_by)) {
                $interview->scheduled_by = auth()->id();
            }
        });

        static::updating(function ($interview) {
            if (auth()->check() && empty($interview->updated_by)) {
                $interview->updated_by = auth()->id();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'scheduled_date', 'start_time', 'mode'])
            ->logOnlyDirty();
    }

    public function getDisplayTypeAttribute(): string
    {
        return config("recruitment.interview.types.{$this->round_type}", $this->round_type);
    }

    public function getDisplayModeAttribute(): string
    {
        return config("recruitment.interview.modes.{$this->mode}", $this->mode);
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Scheduled',
            'confirmed' => 'Confirmed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            'rescheduled' => 'Rescheduled',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'blue',
            'confirmed' => 'green',
            'in_progress' => 'amber',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'warning',
            'rescheduled' => 'purple',
            default => 'gray',
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('g:i A') . ' - ' . Carbon::parse($this->end_time)->format('g:i A');
    }

    public function getIsUpcomingAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed'])
            && $this->scheduled_date >= now()->startOfDay();
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed'])
            && $this->scheduled_date < now()->startOfDay();
    }

    public function getCalendarEventAttribute(): array
    {
        return [
            'id' => $this->id,
            'title' => "{$this->display_type} - {$this->candidate->full_name}",
            'start' => $this->scheduled_date->format('Y-m-d') . 'T' . $this->start_time,
            'end' => $this->scheduled_date->format('Y-m-d') . 'T' . $this->end_time,
            'color' => match ($this->round_type) {
                'hr_screening' => '#0ea5e9',
                'technical' => '#8b5cf6',
                'manager' => '#f59e0b',
                'final' => '#10b981',
                default => '#6b7280',
            },
            'extendedProps' => [
                'candidate' => $this->candidate->full_name,
                'job' => $this->jobPosting->title,
                'type' => $this->display_type,
                'mode' => $this->display_mode,
            ],
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function interviewers()
    {
        return $this->belongsToMany(User::class, 'interview_interviewers')
            ->withPivot(['is_primary', 'is_required', 'response_status', 'decline_reason'])
            ->withTimestamps();
    }

    public function primaryInterviewer(): ?User
    {
        return $this->interviewers()
            ->wherePivot('is_primary', true)
            ->first();
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(InterviewFeedback::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_date', '>=', now()->startOfDay());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', now());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForInterviewer($query, int $userId)
    {
        return $query->whereHas('interviewers', fn($q) => $q->where('users.id', $userId));
    }

    public const ROUND_TYPES = [
        'hr_screening' => 'HR Screening',
        'technical' => 'Technical Round',
        'manager' => 'Manager Round',
        'cultural' => 'Cultural Fit',
        'final' => 'Final Round',
        'panel' => 'Panel Interview',
    ];

    public const MODES = [
        'in_person' => 'In-Person',
        'video_call' => 'Video Call',
        'phone' => 'Phone',
    ];

    public const VIDEO_PLATFORMS = [
        'zoom' => 'Zoom',
        'google_meet' => 'Google Meet',
        'microsoft_teams' => 'Microsoft Teams',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'confirmed' => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
        'rescheduled' => 'Rescheduled',
    ];
}
