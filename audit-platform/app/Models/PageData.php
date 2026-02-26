<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageData extends Model
{
    protected $fillable = [
        'audit_id', 'url', 'status_code', 'load_time_ms',
        'title', 'meta_description', 'h1',
        'images_total', 'images_missing_alt', 'broken_links_count',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    // Timp formatat: 1250 -> "1.25s"
    public function formattedLoadTime(): string
    {
        return number_format($this->load_time_ms / 1000, 2) . 's';
    }
}