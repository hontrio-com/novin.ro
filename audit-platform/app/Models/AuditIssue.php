<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditIssue extends Model
{
    protected $fillable = [
        'audit_id', 'category', 'severity',
        'title', 'description', 'suggestion', 'affected_url', 'impact',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    // Returnează icon în funcție de severitate
    public function severityIcon(): string
    {
        return match($this->severity) {
            'critical' => '🔴',
            'warning'  => '🟡',
            'info'     => '🟢',
            default    => '⚪',
        };
    }

    // Returnează label categorie în română
    public function categoryLabel(): string
    {
        return match($this->category) {
            'technical' => 'Tehnic & Viteză',
            'seo'       => 'SEO',
            'legal'     => 'Legal & GDPR',
            'eeeat'     => 'E-E-A-T',
            'content'   => 'Conținut AI',
            'ux'        => 'UX & Design',
            default     => $this->category,
        };
    }
}