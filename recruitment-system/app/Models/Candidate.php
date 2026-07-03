<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Candidate extends Authenticatable
{
    use HasFactory, SoftDeletes, LogsActivity, Notifiable;

    protected $fillable = [
        'candidate_number',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'password_set_at',
        'email_verified_at',
        'phone',
        'alternate_phone',
        'photograph',
        'gender',
        'date_of_birth',
        'nationality',
        'marital_status',
        'current_address',
        'permanent_address',
        'city',
        'state',
        'country',
        'postal_code',
        'current_company',
        'current_designation',
        'current_salary',
        'expected_salary',
        'salary_currency',
        'notice_period',
        'total_experience_years',
        'highest_qualification',
        'university',
        'passing_year',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'website_url',
        'behance_url',
        'dribbble_url',
        'resume_path',
        'cover_letter_path',
        'resume_original_name',
        'source',
        'referral_code',
        'referral_employee_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'ip_address',
        'user_agent',
        'browser',
        'operating_system',
        'device',
        'converted_to_employee_id',
        'converted_at',
        'notes',
        'blacklist_status',
        'blacklist_reason',
        'is_duplicate',
        'original_candidate_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'converted_at' => 'datetime',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2',
        'total_experience_years' => 'decimal:1',
        'passing_year' => 'integer',
        'is_duplicate' => 'boolean',
        'password' => 'hashed',
        'password_set_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'blacklist_status'])
            ->logOnlyDirty();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($candidate) {
            if (empty($candidate->candidate_number)) {
                $candidate->candidate_number = static::generateCandidateNumber();
            }
        });
    }

    public static function generateCandidateNumber(): string
    {
        $year = now()->year;
        $prefix = 'CAND';
        $lastCandidate = static::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastCandidate ? ((int) substr($lastCandidate->candidate_number, -6)) + 1 : 1;

        return sprintf("%s-%s-%06d", $prefix, $year, $sequence);
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);
        return implode(' ', $parts);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) return null;
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $this->phone);
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) return null;
        return $this->date_of_birth->age;
    }

    public function getPhotographUrlAttribute(): ?string
    {
        if (!$this->photograph) return null;
        return asset('storage/' . $this->photograph);
    }

    public function getResumeUrlAttribute(): ?string
    {
        if (!$this->resume_path) return null;
        return asset('storage/' . $this->resume_path);
    }

    public function getSocialLinksAttribute(): array
    {
        $links = [];
        if ($this->linkedin_url) $links['linkedin'] = $this->linkedin_url;
        if ($this->github_url) $links['github'] = $this->github_url;
        if ($this->portfolio_url) $links['portfolio'] = $this->portfolio_url;
        if ($this->website_url) $links['website'] = $this->website_url;
        if ($this->behance_url) $links['behance'] = $this->behance_url;
        if ($this->dribbble_url) $links['dribbble'] = $this->dribbble_url;
        return $links;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function education(): HasMany
    {
        return $this->hasMany(CandidateEducation::class)->orderBy('sort_order');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class)->orderByDesc('start_date');
    }

    public function currentExperience(): ?CandidateExperience
    {
        return $this->experiences()->where('is_current', true)->first();
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function technicalSkills(): HasMany
    {
        return $this->skills()->whereHas('skill', fn($q) => $q->where('category', 'technical'));
    }

    public function softSkills(): HasMany
    {
        return $this->skills()->whereHas('skill', fn($q) => $q->where('category', 'soft'));
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function latestApplication(): ?Application
    {
        return $this->applications()->latest()->first();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function documents(): HasMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function convertedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_to_employee_id');
    }

    public function originalCandidate(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_candidate_id');
    }

    public function duplicateCandidates(): HasMany
    {
        return $this->hasMany(self::class, 'original_candidate_id');
    }

    public function talentPools(): BelongsTo
    {
        return $this->belongsToMany(TalentPool::class, 'talent_pool_candidates')
            ->withPivot(['notes', 'added_by'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('blacklist_status', '!=', 'blacklisted');
    }

    public function scopeNotBlacklisted($query)
    {
        return $query->where('blacklist_status', '!=', 'blacklisted');
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('blacklist_status', 'blacklisted');
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('current_company', 'like', "%{$term}%")
                ->orWhere('current_designation', 'like', "%{$term}%");
        });
    }

    public function scopeWithFilters($query, array $filters)
    {
        return $query
            ->when($filters['source'] ?? null, fn($q, $s) => $q->bySource($s))
            ->when($filters['experience_min'] ?? null, fn($q, $e) => $q->where('total_experience_years', '>=', $e))
            ->when($filters['experience_max'] ?? null, fn($q, $e) => $q->where('total_experience_years', '<=', $e))
            ->when($filters['notice_period'] ?? null, fn($q, $n) => $q->where('notice_period', $n))
            ->when($filters['location'] ?? null, fn($q, $l) => $q->where(function ($sq) use ($l) {
                $sq->where('city', $l)->orWhere('state', $l)->orWhere('country', $l);
            }));
    }

    public const GENDERS = [
        'male' => 'Male',
        'female' => 'Female',
        'non_binary' => 'Non-Binary',
        'prefer_not_to_say' => 'Prefer Not to Say',
    ];

    public const MARITAL_STATUSES = [
        'single' => 'Single',
        'married' => 'Married',
        'divorced' => 'Divorced',
        'widowed' => 'Widowed',
        'separated' => 'Separated',
    ];

    public const NOTICE_PERIODS = [
        'immediate' => 'Immediate',
        '15_days' => '15 Days',
        '30_days' => '30 Days',
        '60_days' => '60 Days',
        '90_days' => '90 Days',
        'more_than_90' => 'More than 90 Days',
    ];

    public const SOURCES = [
        'careers_page' => 'Careers Page',
        'linkedin' => 'LinkedIn',
        'indeed' => 'Indeed',
        'referral' => 'Employee Referral',
        'agency' => 'Recruitment Agency',
        'job_fair' => 'Job Fair',
        'campus' => 'Campus Placement',
        'social_media' => 'Social Media',
        'other' => 'Other',
    ];
}
