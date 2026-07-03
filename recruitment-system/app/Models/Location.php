<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'city',
        'state',
        'country',
        'country_code',
        'postal_code',
        'latitude',
        'longitude',
        'type',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->name,
            $this->city,
            $this->state,
            $this->country,
        ]);
        return implode(', ', $parts);
    }
}
