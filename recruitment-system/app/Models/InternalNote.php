<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'user_id',
        'content',
        'type',
        'is_private',
        'is_pinned',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    public function scopeScreening($query)
    {
        return $query->where('type', 'screening');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNonPrivate($query)
    {
        return $query->where('is_private', false);
    }

    public const TYPES = [
        'general' => 'General',
        'screening' => 'Screening',
        'interview' => 'Interview',
        'offer' => 'Offer',
        'concern' => 'Concern',
        'highlight' => 'Highlight',
    ];
}
