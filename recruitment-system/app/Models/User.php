<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'department_id',
        'designation_id',
        'reporting_manager_id',
        'joining_date',
        'status',
        'is_admin',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'is_admin' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'status', 'department_id', 'designation_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin || $this->hasAnyRole(['super_admin', 'hr_admin', 'recruiter', 'hiring_manager', 'interviewer']);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
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

    public function directReports(): HasMany
    {
        return $this->hasMany(User::class, 'reporting_manager_id');
    }

    public function managedJobs(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'hiring_manager_id');
    }

    public function assignedJobs(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'recruiter_id');
    }

    public function interviewsAsInterviewer()
    {
        return $this->belongsToMany(Interview::class, 'interview_interviewers', 'user_id', 'interview_id')
            ->withPivot(['is_primary', 'is_required', 'response_status', 'decline_reason'])
            ->withTimestamps();
    }

    public function interviewFeedbacks(): HasMany
    {
        return $this->hasMany(InterviewFeedback::class, 'interviewer_id');
    }

    public function createdOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ApplicationActivity::class, 'causer_id');
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(InternalNote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeHrStaff($query)
    {
        return $query->whereHas('roles', fn($q) => $q->whereIn('name', ['hr_admin', 'recruiter', 'hiring_manager']));
    }
}
