<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'application_status_history';

    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
        'ip_address',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getFromStatusLabelAttribute(): string
    {
        return Application::STATUSES[$this->from_status] ?? $this->from_status;
    }

    public function getToStatusLabelAttribute(): string
    {
        return Application::STATUSES[$this->to_status] ?? $this->to_status;
    }

    public function getTimeInPreviousStatusAttribute(): ?string
    {
        if (!$this->from_status) return null;

        $previous = static::where('application_id', $this->application_id)
            ->where('to_status', $this->from_status)
            ->where('id', '<', $this->id)
            ->orderByDesc('id')
            ->first();

        if (!$previous) return null;

        $days = $previous->created_at->diffInDays($this->created_at);

        if ($days === 0) return '< 1 day';
        if ($days === 1) return '1 day';
        return "{$days} days";
    }

    public function scopeForApplication($query, int $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }
}
