<?php
namespace App\Mail;

use App\Models\Audit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuditCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Audit $audit) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Raportul tău de audit este gata — ' . $this->audit->url,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.audit-completed',
            with: [
                'audit'       => $this->audit,
                'reportUrl'   => route('audit.report', $this->audit->public_token),
                'score'       => $this->audit->score_total,
                'critical'    => $this->audit->issues->where('severity', 'critical')->count(),
                'warnings'    => $this->audit->issues->where('severity', 'warning')->count(),
            ]
        );
    }
}