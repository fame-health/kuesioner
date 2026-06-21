<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public const TYPE_SHORT_TEXT = 'short_text';

    public const TYPE_PARAGRAPH = 'paragraph';

    public const TYPE_RADIO = 'radio';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPE_DROPDOWN = 'dropdown';

    public const TYPES = [
        self::TYPE_SHORT_TEXT => 'Jawaban singkat',
        self::TYPE_PARAGRAPH => 'Paragraf',
        self::TYPE_RADIO => 'Pilihan ganda',
        self::TYPE_CHECKBOX => 'Checkbox',
        self::TYPE_DROPDOWN => 'Dropdown',
    ];

    protected $fillable = [
        'questionnaire_id',
        'question_text',
        'question_type',
        'is_required',
        'show_public_analysis',
        'order_number',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'show_public_analysis' => 'boolean',
            'order_number' => 'integer',
        ];
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function usesOptions(): bool
    {
        return in_array($this->question_type, [
            self::TYPE_RADIO,
            self::TYPE_CHECKBOX,
            self::TYPE_DROPDOWN,
        ], true);
    }
}
