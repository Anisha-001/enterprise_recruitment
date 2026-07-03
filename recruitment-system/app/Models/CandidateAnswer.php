<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'screening_question_id',
        'answer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function screeningQuestion(): BelongsTo
    {
        return $this->belongsTo(ScreeningQuestion::class);
    }
}
