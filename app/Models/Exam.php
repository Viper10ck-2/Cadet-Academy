<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'type',
        'participant_ids',
        'question_composition',
        'description',
        'duration_minutes',
        'passing_score',
        'start_time',
        'end_time',
        'is_active',
        'shuffle_questions',
        'show_result',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
            'shuffle_questions' => 'boolean',
            'show_result' => 'boolean',
            'participant_ids' => 'array',
            'question_composition' => 'array',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    public function getQuestionCountAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && now()->between($this->start_time, $this->end_time);
    }
}
