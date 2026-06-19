<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'question_id',
        'answer_text',
        'answer_json',
    ];

    protected function casts(): array
    {
        return [
            'answer_json' => 'array',
        ];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function getDisplayAnswerAttribute(): string
    {
        if ($this->answer_json !== null) {
            return implode(', ', $this->answer_json);
        }

        return (string) $this->answer_text;
    }
}
