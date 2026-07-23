<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'question_text',
        'type',
        'category',
        'options',
        'correct_answer',
        'points',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function getOptionsArrayAttribute(): array
    {
        return $this->options ?? [];
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'TIU' => 'Tes Intelegensi Umum',
            'TWK' => 'Tes Wawasan Kebangsaan',
            'TKP' => 'Tes Karakteristik Pribadi',
            'TBI' => 'Tes Bahasa Inggris',
            default => 'Tidak Diklasifikasikan',
        };
    }

    public function getCategoryBadgeAttribute(): string
    {
        $colors = [
            'TIU' => 'blue',
            'TWK' => 'red',
            'TKP' => 'green',
            'TBI' => 'amber',
        ];
        $color = $colors[$this->category] ?? 'gray';
        return "<span class=\"text-xs font-medium px-2 py-0.5 rounded bg-{$color}-100 text-{$color}-700 dark:bg-{$color}-900/30 dark:text-{$color}-400\">{$this->category}</span>";
    }
}
