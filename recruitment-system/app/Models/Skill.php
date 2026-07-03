<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Skill extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function jobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'job_skills')
            ->withPivot(['proficiency', 'is_required', 'years_experience', 'sort_order'])
            ->withTimestamps();
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public const CATEGORIES = [
        'technical' => 'Technical Skills',
        'soft' => 'Soft Skills',
        'language' => 'Languages',
        'tool' => 'Tools',
        'framework' => 'Frameworks',
        'database' => 'Databases',
        'cloud' => 'Cloud Platforms',
        'devops' => 'DevOps',
    ];
}
