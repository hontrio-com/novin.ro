<?php
namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Payment;
use App\Jobs\RunAuditJob;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Plată confirmată
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $auditId = $session->metadata->audit_id;

            $audit = Audit::find($auditId);
            if (!$audit) return response()->json(['ok' => true]);

            $payment = Payment::where('audit_id', $auditId)->first();
            if ($payment && !$payment->isPaid()) {
                $payment->update([
                    'status'                => 'paid',
                    'stripe_payment_intent' => $session->payment_intent,
                    'paid_at'               => now(),
                ]);

                $audit->update(['status' => 'processing']);

                // Pornește auditul dacă nu e deja pornit
                RunAuditJob::dispatch($audit);
            }
        }

        return response()->json(['ok' => true]);
    }
}