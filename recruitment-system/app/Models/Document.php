<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'name',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'disk',
        'collection',
        'description',
        'meta',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'meta' => 'array',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    public function getFileIconAttribute(): string
    {
        return match (strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION))) {
            'pdf' => 'document-text',
            'doc', 'docx' => 'document',
            'xls', 'xlsx' => 'table-cells',
            'jpg', 'jpeg', 'png', 'gif' => 'photo',
            'zip', 'rar' => 'folder',
            default => 'paper-clip',
        };
    }

    public function scopeForCollection($query, string $collection)
    {
        return $query->where('collection', $collection);
    }

    public function scopeForModel($query, string $type, int $id)
    {
        return $query->where('documentable_type', $type)->where('documentable_id', $id);
    }

    public const COLLECTIONS = [
        'default' => 'General',
        'resume' => 'Resume',
        'cover_letter' => 'Cover Letter',
        'certificates' => 'Certificates',
        'identity' => 'Identity Proof',
        'portfolio' => 'Portfolio',
        'offer_letter' => 'Offer Letter',
        'interview' => 'Interview',
    ];
}
