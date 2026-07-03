<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'website',
        'contact_person',
        'contact_email',
        'contact_phone',
        'cost_per_hire',
        'is_active',
    ];

    protected $casts = [
        'cost_per_hire' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public const TYPES = [
        'job_board' => 'Job Board',
        'social_media' => 'Social Media',
        'referral' => 'Employee Referral',
        'agency' => 'Recruitment Agency',
        'campus' => 'Campus',
        'direct' => 'Direct',
        'event' => 'Event',
        'other' => 'Other',
    ];
}
