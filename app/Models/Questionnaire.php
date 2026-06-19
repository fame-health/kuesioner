<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'public_token',
        'is_active',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expired_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Questionnaire $questionnaire): void {
            if (blank($questionnaire->public_token)) {
                $questionnaire->public_token = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order_number');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public function publicUrl(): string
    {
        return route('questionnaires.public.show', $this->public_token);
    }
}
