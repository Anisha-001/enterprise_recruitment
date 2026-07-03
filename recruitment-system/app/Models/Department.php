<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Department extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'head_id',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function activeJobPostings(): HasMany
    {
        return $this->jobPostings()->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
