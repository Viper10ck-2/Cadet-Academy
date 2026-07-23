<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamParticipant extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'token',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
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

    /** Generate unique 8-char token */
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (static::where('token', $token)->exists());

        return $token;
    }
}
