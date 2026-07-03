<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Offer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'offer_number',
        'application_id',
        'candidate_id',
        'job_posting_id',
        'status',
        'version',
        'previous_version_id',
        'proposed_designation',
        'department_id',
        'designation_id',
        'reporting_manager_id',
        'location_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'medical_allowance',
        'other_allowances',
        'bonus_percentage',
        'total_ctc',
        'salary_currency',
        'salary_period',
        'joining_date',
        'offer_expiry_date',
        'proposed_joining_date',
        'special_conditions',
        'notes',
        'rejection_reason',
        'pdf_path',
        'digital_signature',
        'signed_at',
        'signed_ip',
        'sent_at',
        'sent_by',
        'responded_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'bonus_percentage' => 'decimal:2',
        'total_ctc' => 'decimal:2',
        'joining_date' => 'date',
        'offer_expiry_date' => 'date',
        'proposed_joining_date' => 'date',
        'signed_at' => 'datetime',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'version' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'basic_salary', 'total_ctc', 'joining_date'])
            ->logOnlyDirty();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($offer) {
            if ($offer->application_id) {
                $application = $offer->application;
                if ($application) {
                    $offer->candidate_id = $application->candidate_id;
                    $offer->job_posting_id = $application->job_posting_id;
                }
            }
            if (empty($offer->proposed_joining_date) && !empty($offer->joining_date)) {
                $offer->proposed_joining_date = $offer->joining_date;
            }
        });

        static::creating(function ($offer) {
            if (empty($offer->offer_number)) {
                $offer->offer_number = static::generateOfferNumber();
            }
            if (empty($offer->version)) {
                $offer->version = 1;
            }
            if (auth()->check() && empty($offer->created_by)) {
                $offer->created_by = auth()->id();
            }
        });

        static::updating(function ($offer) {
            if (auth()->check() && empty($offer->updated_by)) {
                $offer->updated_by = auth()->id();
            }
        });
    }

    public static function generateOfferNumber(): string
    {
        $year = now()->year;
        $prefix = 'OFF';
        $lastOffer = static::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOffer ? ((int) substr($lastOffer->offer_number, -6)) + 1 : 1;

        return sprintf("%s-%s-%06d", $prefix, $year, $sequence);
    }

    public function getDisplayStatusAttribute(): string
    {
        return static::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'primary',
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired' => 'warning',
            'negotiating' => 'info',
            'withdrawn' => 'neutral',
            default => 'gray',
        };
    }

    public function getTotalCompensationBreakdownAttribute(): array
    {
        return [
            'basic_salary' => $this->basic_salary,
            'housing' => $this->housing_allowance,
            'transport' => $this->transport_allowance,
            'medical' => $this->medical_allowance,
            'other' => $this->other_allowances,
            'total' => $this->total_ctc,
        ];
    }

    public function getMonthlyCtcAttribute(): float
    {
        return match ($this->salary_period) {
            'monthly' => (float) $this->total_ctc,
            'yearly' => round((float) $this->total_ctc / 12, 2),
            'weekly' => (float) $this->total_ctc * 4.33,
            'daily' => (float) $this->total_ctc * 30,
            'hourly' => (float) $this->total_ctc * 2080 / 12,
            default => (float) $this->total_ctc,
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'sent'
            && $this->offer_expiry_date
            && $this->offer_expiry_date->isPast();
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function previousVersion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_version_id');
    }

    public function nextVersion(): HasOne
    {
        return $this->hasOne(self::class, 'previous_version_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'negotiating']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'sent')
            ->where('offer_expiry_date', '<', now());
    }

    public function markAsSent(int $userId): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_by' => $userId,
        ]);
    }

    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    public function markAsRejected(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'responded_at' => now(),
        ]);
    }

    public function createNewVersion(array $changes): self
    {
        $newOffer = $this->replicate(['offer_number', 'pdf_path', 'digital_signature', 'signed_at', 'signed_ip', 'sent_at', 'sent_by', 'responded_at']);
        $newOffer->previous_version_id = $this->id;
        $newOffer->version = $this->version + 1;
        $newOffer->status = 'draft';
        $newOffer->fill($changes);
        $newOffer->save();

        return $newOffer;
    }

    public function generatePdf(): string
    {
        return app(\App\Services\Offer\OfferPdfService::class)->generate($this);
    }

    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'negotiating' => 'Negotiating',
        'withdrawn' => 'Withdrawn',
    ];
}
