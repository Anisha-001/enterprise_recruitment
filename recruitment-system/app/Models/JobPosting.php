<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes, HasSlug, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'department_id',
        'designation_id',
        'location_id',
        'hiring_manager_id',
        'recruiter_id',
        'employment_type',
        'experience_level',
        'work_arrangement',
        'min_experience_years',
        'max_experience_years',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        'show_salary',
        'vacancies',
        'published_at',
        'closing_date',
        'apply_before_days',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'is_urgent',
        'status',
        'requisition_number',
        'source',
        'external_job_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'date',
        'closing_date' => 'date',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'show_salary' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'vacancies' => 'integer',
        'min_experience_years' => 'integer',
        'max_experience_years' => 'integer',
        'apply_before_days' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($job) {
            if (auth()->check() && empty($job->created_by)) {
                $job->created_by = auth()->id();
            }
        });

        static::updating(function ($job) {
            if (auth()->check() && empty($job->updated_by)) {
                $job->updated_by = auth()->id();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'department_id', 'vacancies'])
            ->logOnlyDirty();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSalaryRangeAttribute(): ?string
    {
        if (!$this->show_salary || (!$this->min_salary && !$this->max_salary)) {
            return null;
        }

        $currency = $this->salary_currency;
        $period = match ($this->salary_period) {
            'hourly' => '/hr',
            'daily' => '/day',
            'weekly' => '/wk',
            'monthly' => '/mo',
            default => '/yr',
        };

        if ($this->min_salary && $this->max_salary) {
            return "{$currency} " . number_format($this->min_salary) . ' - ' . number_format($this->max_salary) . $period;
        }

        return "{$currency} " . number_format($this->min_salary ?? $this->max_salary) . $period;
    }

    public function getExperienceRangeAttribute(): string
    {
        if ($this->min_experience_years === 0 && !$this->max_experience_years) {
            return 'Fresher';
        }

        if ($this->min_experience_years && $this->max_experience_years) {
            return "{$this->min_experience_years} - {$this->max_experience_years} years";
        }

        return $this->min_experience_years . '+ years';
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function hiringManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hiring_manager_id');
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
            ->withPivot(['proficiency', 'is_required', 'years_experience', 'sort_order'])
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    public function requiredSkills(): BelongsToMany
    {
        return $this->skills()->wherePivot('is_required', true);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function activeApplications(): HasMany
    {
        return $this->applications()->whereNotIn('status', ['hired', 'rejected', 'withdrawn']);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function screeningQuestions(): HasMany
    {
        return $this->hasMany(ScreeningQuestion::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('closing_date')
                    ->orWhere('closing_date', '>=', now());
            });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByEmploymentType($query, string $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('requirements', 'like', "%{$term}%")
                ->orWhereHas('department', fn($d) => $d->where('name', 'like', "%{$term}%"))
                ->orWhereHas('skills', fn($s) => $s->where('name', 'like', "%{$term}%"));
        });
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        if ($this->closing_date && $this->closing_date->isPast()) {
            return false;
        }

        return true;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->closing_date) {
            return null;
        }
        return max(0, now()->diffInDays($this->closing_date, false));
    }

    public function getApplicationCountAttribute(): int
    {
        return $this->applications()->count();
    }

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
        'freelance' => 'Freelance',
    ];

    public const EXPERIENCE_LEVELS = [
        'entry' => 'Entry Level',
        'mid' => 'Mid Level',
        'senior' => 'Senior Level',
        'lead' => 'Lead',
        'manager' => 'Manager',
        'director' => 'Director',
    ];

    public const WORK_ARRANGEMENTS = [
        'on_site' => 'On-Site',
        'hybrid' => 'Hybrid',
        'remote' => 'Remote',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
        'closed' => 'Closed',
        'archived' => 'Archived',
        'on_hold' => 'On Hold',
    ];

    public const SALARY_PERIODS = [
        'hourly' => 'Per Hour',
        'daily' => 'Per Day',
        'weekly' => 'Per Week',
        'monthly' => 'Per Month',
        'yearly' => 'Per Year',
    ];
}
