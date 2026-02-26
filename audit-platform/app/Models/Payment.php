<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'audit_id', 'amount', 'currency', 'status',
        'stripe_session_id', 'stripe_payment_intent', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    // Sumă formatată: 20000 -> "200.00 RON"
    public function formattedAmount(): string
    {
        return number_format($this->amount / 100, 2) . ' ' . $this->currency;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}