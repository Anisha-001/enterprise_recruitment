<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Application extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'application_number',
        'candidate_id',
        'job_posting_id',
        'recruiter_id',
        'status',
        'rejection_reason',
        'rejection_notes',
        'rejected_by',
        'rejected_at',
        'expected_salary',
        'offered_salary',
        'expected_joining_date',
        'offered_joining_date',
        'actual_joining_date',
        'stage_progress',
        'rating',
        'screening_notes',
        'internal_notes',
        'recruiter_notes',
        'is_new',
        'reviewed_at',
        'reviewed_by',
        'status_changed_at',
        'status_changed_by',
        'notifications_enabled',
        'last_notification_sent_at',
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
        'offered_salary' => 'decimal:2',
        'expected_joining_date' => 'date',
        'offered_joining_date' => 'date',
        'actual_joining_date' => 'date',
        'rejected_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'last_notification_sent_at' => 'datetime',
        'is_new' => 'boolean',
        'notifications_enabled' => 'boolean',
        'stage_progress' => 'integer',
        'rating' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'rating', 'recruiter_id'])
            ->logOnlyDirty();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($application) {
            if (empty($application->application_number)) {
                $application->application_number = static::generateApplicationNumber();
            }
        });

        static::updating(function ($application) {
            if ($application->isDirty('status')) {
                $application->status_changed_at = now();
            }
        });
    }

    public static function generateApplicationNumber(): string
    {
        $year = now()->year;
        $prefix = config('recruitment.application.prefix', 'APP');
        $format = config('recruitment.application.number_format', '%s-%s-%06d');

        $lastApp = static::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastApp ? ((int) substr($lastApp->application_number, -6)) + 1 : 1;

        return sprintf($format, $prefix, $year, $sequence);
    }

    public function getDisplayStatusAttribute(): string
    {
        return static::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'gray',
            'screening' => 'blue',
            'shortlisted' => 'indigo',
            'technical_interview', 'manager_interview', 'final_interview' => 'orange',
            'offer_pending', 'offer_sent' => 'amber',
            'offer_accepted', 'hired' => 'green',
            'rejected', 'offer_rejected' => 'red',
            'withdrawn' => 'neutral',
            'on_hold' => 'yellow',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'new' => 'document',
            'screening' => 'magnifying-glass',
            'shortlisted' => 'check-circle',
            'technical_interview', 'manager_interview', 'final_interview' => 'users',
            'offer_pending', 'offer_sent' => 'envelope',
            'offer_accepted' => 'hand-thumb-up',
            'hired' => 'briefcase',
            'rejected', 'offer_rejected' => 'x-circle',
            'withdrawn' => 'arrow-left',
            'on_hold' => 'pause-circle',
            default => 'document',
        };
    }

    public function getTimeInStageAttribute(): string
    {
        $lastChange = $this->status_changed_at ?? $this->created_at;
        $days = $lastChange->diffInDays(now());

        if ($days === 0) return 'Today';
        if ($days === 1) return '1 day';
        if ($days < 30) return "{$days} days";

        $months = floor($days / 30);
        return "{$months} month" . ($months > 1 ? 's' : '');
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['hired', 'rejected', 'withdrawn']);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = config("recruitment.pipeline.transitions.{$this->status}", []);
        return in_array($newStatus, $allowed);
    }

    public function getAvailableTransitions(): array
    {
        $transitions = config("recruitment.pipeline.transitions.{$this->status}", []);
        return array_filter($transitions, fn($t) => $t !== $this->status);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusChangedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class)->orderBy('round_number')->orderBy('scheduled_date');
    }

    public function completedInterviews(): HasMany
    {
        return $this->interviews()->where('status', 'completed');
    }

    public function upcomingInterviews(): HasMany
    {
        return $this->interviews()->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_date', '>=', now());
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function latestOffer(): ?Offer
    {
        return $this->offers()->latest()->first();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ApplicationActivity::class)->orderByDesc('created_at');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class)->orderByDesc('created_at');
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(InternalNote::class)->orderByDesc('created_at');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CandidateAnswer::class);
    }

    public function documents(): HasMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function emailLogs(): HasMany
    {
        return $this->morphMany(EmailLog::class, 'emailable');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['hired', 'rejected', 'withdrawn']);
    }

    public function scopeByStatus($query, string|array $status)
    {
        return $query->whereIn('status', (array) $status);
    }

    public function scopeNewApplications($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('recruiter_id', $userId);
    }

    public function scopeForJob($query, int $jobId)
    {
        return $query->where('job_posting_id', $jobId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('application_number', 'like', "%{$term}%")
                ->orWhereHas('candidate', function ($cq) use ($term) {
                    $cq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orWhereHas('jobPosting', fn($jq) => $jq->where('title', 'like', "%{$term}%"));
        });
    }

    public function scopeWithFilters($query, array $filters)
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $s) => $q->byStatus((array) $s))
            ->when($filters['job_id'] ?? null, fn($q, $j) => $q->forJob($j))
            ->when($filters['recruiter_id'] ?? null, fn($q, $r) => $q->assignedTo($r))
            ->when($filters['date_from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($filters['experience_min'] ?? null, fn($q) => $q->whereHas('candidate', fn($cq) => $cq->where('total_experience_years', '>=', $filters['experience_min'])))
            ->when($filters['experience_max'] ?? null, fn($q) => $q->whereHas('candidate', fn($cq) => $cq->where('total_experience_years', '<=', $filters['experience_max'])))
            ->when($filters['notice_period'] ?? null, fn($q) => $q->whereHas('candidate', fn($cq) => $cq->where('notice_period', $filters['notice_period'])))
            ->when($filters['department_id'] ?? null, fn($q) => $q->whereHas('jobPosting', fn($jq) => $jq->where('department_id', $filters['department_id'])))
            ->when($filters['is_new'] ?? null, fn($q) => $q->newApplications());
    }

    public const STATUSES = [
        'new' => 'New',
        'screening' => 'Screening',
        'shortlisted' => 'Shortlisted',
        'technical_interview' => 'Technical Interview',
        'manager_interview' => 'Manager Interview',
        'final_interview' => 'Final Interview',
        'offer_pending' => 'Offer Pending',
        'offer_sent' => 'Offer Sent',
        'offer_accepted' => 'Offer Accepted',
        'offer_rejected' => 'Offer Rejected',
        'hired' => 'Hired',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
        'on_hold' => 'On Hold',
    ];

    public const REJECTION_REASONS = [
        'underqualified' => 'Underqualified',
        'overqualified' => 'Overqualified',
        'poor_interview' => 'Poor Interview Performance',
        'salary_mismatch' => 'Salary Mismatch',
        'position_filled' => 'Position Filled',
        'candidate_withdrew' => 'Candidate Withdrew',
        'failed_background_check' => 'Failed Background Check',
        'location_mismatch' => 'Location Mismatch',
        'no_response' => 'No Response',
        'other' => 'Other',
    ];

    public const PIPELINE_STAGES = [
        ['key' => 'new', 'label' => 'Applied', 'icon' => 'document-text'],
        ['key' => 'screening', 'label' => 'Screening', 'icon' => 'magnifying-glass'],
        ['key' => 'shortlisted', 'label' => 'Shortlisted', 'icon' => 'check'],
        ['key' => 'technical_interview', 'label' => 'Technical', 'icon' => 'code-bracket'],
        ['key' => 'manager_interview', 'label' => 'Manager', 'icon' => 'user-group'],
        ['key' => 'final_interview', 'label' => 'Final', 'icon' => 'star'],
        ['key' => 'offer_pending', 'label' => 'Offer', 'icon' => 'envelope'],
        ['key' => 'offer_sent', 'label' => 'Offer Sent', 'icon' => 'paper-airplane'],
        ['key' => 'offer_accepted', 'label' => 'Accepted', 'icon' => 'hand-thumb-up'],
        ['key' => 'hired', 'label' => 'Hired', 'icon' => 'briefcase'],
    ];
}
