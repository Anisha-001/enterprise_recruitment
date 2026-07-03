<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScreeningQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'question',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CandidateAnswer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public const TYPES = [
        'text' => 'Short Text',
        'textarea' => 'Long Text',
        'yes_no' => 'Yes / No',
        'number' => 'Number',
        'date' => 'Date',
        'single_choice' => 'Single Choice',
        'multiple_choice' => 'Multiple Choice',
    ];
}
