@component('mail::message')
# Contul tau Inovex Audit a fost creat

Buna ziua, **{{ $user->name }}**,

Raportul de audit pentru **{{ $audit->url }}** este gata. Ti-am creat automat un cont pe platforma Inovex Audit unde poti accesa raportul oricand in urmatoarele 30 de zile.

---

## Datele tale de conectare

**Email:** {{ $user->email }}
**Parola temporara:** `{{ $password }}`

@component('mail::button', ['url' => route('login'), 'color' => 'dark'])
Conecteaza-te la cont
@endcomponent

**Important:** Iti recomandam sa iti schimbi parola dupa prima conectare din sectiunea Setari.

---

## Raportul tau

**Scor general: {{ $audit->score_total ?? 0 }}/100**

@component('mail::button', ['url' => route('audit.report', $audit->public_token), 'color' => 'blue'])
Vezi Raportul Complet
@endcomponent

Raportul ramane disponibil **30 de zile** de la data generarii.

---

Ai intrebari? Contacteaza-ne la **contact@inovex.ro** sau **0750 456 096**.

Cu stima,
**Echipa Inovex.ro**
@endcomponent