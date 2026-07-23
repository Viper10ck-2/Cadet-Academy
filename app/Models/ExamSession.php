<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'finished_at',
        'last_activity_at',
        'score',
        'total_questions',
        'answered_questions',
        'correct_answers',
        'status',
        'question_ids',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'question_ids' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->started_at || $this->status !== 'in_progress') return 0;
        $elapsed = now()->diffInSeconds($this->started_at);
        $total = $this->exam->duration_minutes * 60;
        return max(0, $total - $elapsed);
    }

    public function getIsPassedAttribute(): bool
    {
        return $this->score !== null && $this->score >= $this->exam->passing_score;
    }
}
