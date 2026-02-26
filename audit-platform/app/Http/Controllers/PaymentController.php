<?php
namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Payment;
use App\Jobs\RunAuditJob;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    // Creează sesiunea Stripe și redirectează
    public function checkout(Audit $audit)
    {
        // Dacă plata există deja și e paid, du-l la progress
        if ($audit->payment && $audit->payment->isPaid()) {
            return redirect()->route('audit.progress', $audit);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'ron',
                    'unit_amount'  => 20000, // 200 RON în bani
                    'product_data' => [
                        'name'        => 'Audit AI Complet',
                        'description' => 'Analiză completă a site-ului ' . $audit->url,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('audit.success', $audit) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('home'),
            'metadata'    => [
                'audit_id' => $audit->id,
            ],
        ]);

        // Salvează sesiunea Stripe
        Payment::updateOrCreate(
            ['audit_id' => $audit->id],
            [
                'amount'            => 20000,
                'currency'          => 'RON',
                'status'            => 'pending',
                'stripe_session_id' => $session->id,
            ]
        );

        return redirect($session->url);
    }

    // Stripe redirectează aici după plată
    public function success(Audit $audit, Request $request)
    {
        // Verificăm sesiunea direct la Stripe (nu ne bazăm doar pe redirect)
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($request->session_id);

        if ($session->payment_status === 'paid') {
            $audit->payment->update([
                'status'                => 'paid',
                'stripe_payment_intent' => $session->payment_intent,
                'paid_at'               => now(),
            ]);

            $audit->update(['status' => 'processing']);

            // Pornește jobul de audit în background
            RunAuditJob::dispatch($audit);
        }

        return redirect()->route('audit.progress', $audit);
    }
}