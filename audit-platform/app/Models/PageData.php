<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageData extends Model
{
    protected $fillable = [
        'audit_id', 'url', 'page_type', 'status_code', 'load_time_ms',
        'title', 'meta_description', 'h1',
        'images_total', 'images_missing_alt', 'broken_links_count',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function formattedLoadTime(): string
    {
        if (!$this->load_time_ms) return 'N/A';
        return $this->load_time_ms >= 1000
            ? number_format($this->load_time_ms / 1000, 2) . 's'
            : $this->load_time_ms . 'ms';
    }

    public function statusClass(): string
    {
        return match(true) {
            $this->status_code >= 200 && $this->status_code < 300 => 'good',
            $this->status_code >= 300 && $this->status_code < 400 => 'redirect',
            $this->status_code >= 400                             => 'error',
            default                                               => 'na',
        };
    }

    public function pageTypeLabel(): string
    {
        return match($this->page_type) {
            'home'     => 'Home',
            'contact'  => 'Contact',
            'about'    => 'Despre',
            'services' => 'Servicii',
            'blog'     => 'Blog / Articol',
            'category' => 'Categorie',
            'product'  => 'Produs',
            'checkout' => 'Checkout',
            'faq'      => 'FAQ',
            'legal'    => 'Legal / Politici',
            default    => 'Alta pagina',
        };
    }

    public function pageTypeIcon(): string
    {
        return match($this->page_type) {
            'home'     => '[H]',
            'contact'  => '[C]',
            'about'    => '[A]',
            'services' => '[S]',
            'blog'     => '[B]',
            'category' => '[Cat]',
            'product'  => '[P]',
            'checkout' => '[Pay]',
            'faq'      => '[?]',
            'legal'    => '[L]',
            default    => '[pg]',
        };
    }

    public function loadTimeClass(): string
    {
        if (!$this->load_time_ms) return 'na';
        return match(true) {
            $this->load_time_ms < 1000 => 'good',
            $this->load_time_ms < 2500 => 'needs',
            default                    => 'poor',
        };
    }
}