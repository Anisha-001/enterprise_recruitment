<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateEducation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'candidate_education';

    protected $fillable = [
        'candidate_id',
        'degree_type',
        'degree_name',
        'field_of_study',
        'institution',
        'university',
        'location',
        'start_year',
        'end_year',
        'is_current',
        'grade_cgpa',
        'grade_scale',
        'percentage',
        'description',
        'certificate_path',
        'sort_order',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'is_current' => 'boolean',
        'grade_cgpa' => 'decimal:2',
        'percentage' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getDurationAttribute(): string
    {
        if ($this->is_current) {
            return "{$this->start_year} - Present";
        }
        return "{$this->start_year} - {$this->end_year}";
    }

    public function getDisplayGradeAttribute(): ?string
    {
        if ($this->grade_cgpa) {
            return "CGPA: {$this->grade_cgpa}/{$this->grade_scale}";
        }
        if ($this->percentage) {
            return "{$this->percentage}%";
        }
        return null;
    }

    public const DEGREE_TYPES = [
        'high_school' => 'High School',
        'diploma' => 'Diploma',
        'associate' => 'Associate Degree',
        'bachelor' => "Bachelor's Degree",
        'master' => "Master's Degree",
        'doctorate' => 'Doctorate (PhD)',
        'post_doctorate' => 'Post Doctorate',
        'professional_certification' => 'Professional Certification',
        'other' => 'Other',
    ];
}
