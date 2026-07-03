<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewFeedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'interview_feedbacks';

    protected $fillable = [
        'interview_id',
        'interviewer_id',
        'application_id',
        'technical_skills_rating',
        'communication_rating',
        'problem_solving_rating',
        'cultural_fit_rating',
        'experience_rating',
        'overall_rating',
        'strengths',
        'weaknesses',
        'notes',
        'questions_asked',
        'candidate_responses',
        'recommendation',
        'recommendation_reason',
        'is_submitted',
        'submitted_at',
        'is_confidential',
    ];

    protected $casts = [
        'technical_skills_rating' => 'integer',
        'communication_rating' => 'integer',
        'problem_solving_rating' => 'integer',
        'cultural_fit_rating' => 'integer',
        'experience_rating' => 'integer',
        'overall_rating' => 'integer',
        'is_submitted' => 'boolean',
        'is_confidential' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->technical_skills_rating,
            $this->communication_rating,
            $this->problem_solving_rating,
            $this->cultural_fit_rating,
            $this->experience_rating,
        ]);

        return empty($ratings) ? 0 : round(array_sum($ratings) / count($ratings), 1);
    }

    public function getDisplayRecommendationAttribute(): string
    {
        return match ($this->recommendation) {
            'strong_hire' => 'Strong Hire',
            'hire' => 'Hire',
            'consider' => 'Consider',
            'reject' => 'Reject',
            'strong_reject' => 'Strong Reject',
            default => $this->recommendation,
        };
    }

    public function getRecommendationColorAttribute(): string
    {
        return match ($this->recommendation) {
            'strong_hire' => 'success',
            'hire' => 'success',
            'consider' => 'warning',
            'reject' => 'danger',
            'strong_reject' => 'danger',
            default => 'gray',
        };
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    public function scopeForApplication($query, int $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }

    public function scopeByInterviewer($query, int $interviewerId)
    {
        return $query->where('interviewer_id', $interviewerId);
    }

    public function scopeNonConfidential($query)
    {
        return $query->where('is_confidential', false);
    }

    public function submit(): void
    {
        $this->update([
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);
    }

    public const RECOMMENDATIONS = [
        'strong_hire' => 'Strong Hire',
        'hire' => 'Hire',
        'consider' => 'Consider',
        'reject' => 'Reject',
        'strong_reject' => 'Strong Reject',
    ];

    public const RATING_LABELS = [
        1 => 'Poor',
        2 => 'Below Average',
        3 => 'Average',
        4 => 'Good',
        5 => 'Very Good',
        6 => 'Excellent',
        7 => 'Outstanding',
        8 => 'Exceptional',
        9 => 'Superior',
        10 => 'Perfect',
    ];
}
