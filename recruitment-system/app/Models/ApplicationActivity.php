<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationActivity extends Model
{
    use HasFactory;

    protected $table = 'application_activities';

    protected $fillable = [
        'application_id',
        'causer_type',
        'causer_id',
        'type',
        'icon',
        'color',
        'title',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSystemGenerated($query)
    {
        return $query->where('type', 'system');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public const TYPES = [
        'status_change' => 'Status Change',
        'note' => 'Note Added',
        'interview' => 'Interview',
        'email' => 'Email Sent',
        'call' => 'Phone Call',
        'assessment' => 'Assessment',
        'document' => 'Document',
        'offer' => 'Offer',
        'system' => 'System',
    ];

    public const ICONS = [
        'status_change' => 'arrows-right-left',
        'note' => 'pencil',
        'interview' => 'users',
        'email' => 'envelope',
        'call' => 'phone',
        'assessment' => 'clipboard-document-check',
        'document' => 'document-text',
        'offer' => 'briefcase',
        'system' => 'cog',
    ];

    public const COLORS = [
        'status_change' => 'primary',
        'note' => 'gray',
        'interview' => 'warning',
        'email' => 'info',
        'call' => 'success',
        'assessment' => 'secondary',
        'document' => 'neutral',
        'offer' => 'accent',
        'system' => 'neutral',
    ];
}
