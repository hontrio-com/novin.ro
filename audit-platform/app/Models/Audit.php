<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'email',
        'status',
        'public_token',
        'score_total',
        'score_technical',
        'score_seo',
        'score_legal',
        'score_eeeat',
        'score_content',
        'score_ux',
        'ai_summary',
        'ai_summary_generated_at',
    ];

    protected $casts = [
        'completed_at'           => 'datetime',
        'ai_summary_generated_at'=> 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(AuditIssue::class);
    }

    public function pageData(): HasMany
    {
        return $this->hasMany(PageData::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getScoreColorAttribute(): string
    {
        $s = $this->score_total ?? 0;
        if ($s >= 80) return '#16a34a';
        if ($s >= 50) return '#d97706';
        return '#dc2626';
    }

    public function getScoreLabelAttribute(): string
    {
        $s = $this->score_total ?? 0;
        if ($s >= 80) return 'Bun';
        if ($s >= 50) return 'Mediu';
        return 'Slab';
    }

    public function getCriticalCountAttribute(): int
    {
        return $this->issues->where('severity', 'critical')->count();
    }

    public function getWarningCountAttribute(): int
    {
        return $this->issues->where('severity', 'warning')->count();
    }
}