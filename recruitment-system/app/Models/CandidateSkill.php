<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'skill_id',
        'skill_name',
        'proficiency',
        'years_experience',
        'is_primary',
    ];

    protected $casts = [
        'years_experience' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function getProficiencyLabelAttribute(): string
    {
        return match ($this->proficiency) {
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
            default => $this->proficiency,
        };
    }

    public function getProficiencyColorAttribute(): string
    {
        return match ($this->proficiency) {
            'beginner' => 'gray',
            'intermediate' => 'blue',
            'advanced' => 'indigo',
            'expert' => 'emerald',
            default => 'gray',
        };
    }

    public const PROFICIENCY_LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert',
    ];
}
