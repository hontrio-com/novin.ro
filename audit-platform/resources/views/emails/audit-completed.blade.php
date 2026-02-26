@component('mail::message')

# Raportul tău de audit este gata! 🎉

Bună ziua,

Am finalizat analiza completă pentru **{{ $audit->url }}**.

---

## Scorul general: {{ $score }}/100

@if($score >= 80)
✅ Site-ul tău arată bine, dar am găsit câteva îmbunătățiri.
@elseif($score >= 50)
⚠️ Site-ul tău are potențial neexploatat. Problemele identificate te afectează direct în Google.
@else
🔴 Site-ul tău are probleme serioase care îți afectează vizibilitatea și vânzările.
@endif

**Probleme identificate:**
- 🔴 Critice: {{ $critical }}
- 🟡 Avertismente: {{ $warnings }}

---

@component('mail::button', ['url' => $reportUrl, 'color' => 'blue'])
Vezi Raportul Complet →
@endcomponent

Raportul rămâne disponibil **30 de zile** de la data generării.

---

Ai întrebări sau vrei să implementăm îmbunătățirile pentru tine?
Contactează-ne la **contact@inovex.ro** sau **0750 456 096**.

Cu stimă,
**Echipa Inovex.ro**

@endcomponent