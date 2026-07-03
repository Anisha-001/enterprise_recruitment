<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TalentPool extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'department_id',
        'criteria',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($talentPool) {
            if (auth()->check() && empty($talentPool->created_by)) {
                $talentPool->created_by = auth()->id();
            }
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'talent_pool_candidates')
            ->withPivot(['notes', 'added_by'])
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
