<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CandidateExperience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_id',
        'company_name',
        'designation',
        'department',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'salary',
        'salary_currency',
        'responsibilities',
        'achievements',
        'leave_reason',
        'reference_name',
        'reference_contact',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'salary' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getDurationAttribute(): string
    {
        $end = $this->is_current ? now() : Carbon::parse($this->end_date);
        $start = Carbon::parse($this->start_date);

        $years = $start->diffInYears($end);
        $months = $start->copy()->addYears($years)->diffInMonths($end);

        $parts = [];
        if ($years > 0) $parts[] = "{$years} yr" . ($years > 1 ? 's' : '');
        if ($months > 0) $parts[] = "{$months} mo" . ($months > 1 ? 's' : '');

        return empty($parts) ? '< 1 month' : implode(' ', $parts);
    }

    public function getFormattedDurationAttribute(): string
    {
        $start = Carbon::parse($this->start_date)->format('M Y');
        $end = $this->is_current ? 'Present' : Carbon::parse($this->end_date)->format('M Y');
        return "{$start} - {$end} ({$this->duration})";
    }
}
