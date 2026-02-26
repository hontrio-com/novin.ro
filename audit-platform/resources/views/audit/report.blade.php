@extends('layouts.app')
@section('title', 'Raport Audit — ' . parse_url($audit->url, PHP_URL_HOST))

@push('styles')
<style>
/* HEADER */
.rh { border-bottom: 1px solid var(--rule); padding: 44px 24px 36px; background: var(--paper); position: relative; overflow: hidden; }
.rh::before { content: ''; position: absolute; top: 0; right: 0; width: 500px; height: 260px; background: radial-gradient(ellipse at 100% 0%, rgba(45,145,206,.06) 0%, transparent 60%); pointer-events: none; }
.rh-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: start; position: relative; z-index: 1; }
.rh-bread { font-size: 12px; color: var(--ink-5); display: flex; align-items: center; gap: 6px; margin-bottom: 12px; }
.rh-bread a { color: var(--ink-5); text-decoration: none; } .rh-bread a:hover { color: var(--ink-3); }
.rh-url { font-size: 26px; font-weight: 800; letter-spacing: -1.5px; color: var(--ink); margin-bottom: 14px; line-height: 1; }
.rh-tags { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 18px; }
.rh-meta { font-size: 12px; color: var(--ink-5); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.rh-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--ink-6); }

/* Score widget */
.score-w { text-align: center; flex-shrink: 0; }
.score-lbl { font-size: 11px; color: var(--ink-5); font-weight: 600; letter-spacing: .3px; text-transform: uppercase; margin-bottom: 8px; }
.score-ring { position: relative; width: 110px; height: 110px; margin: 0 auto 10px; }
.score-ring svg { width: 110px; height: 110px; transform: rotate(-90deg); }
.score-track { fill: none; stroke: var(--rule); stroke-width: 6; }
.score-fill  { fill: none; stroke-width: 6; stroke-linecap: round; stroke-dasharray: 314; transition: stroke-dashoffset 1.2s var(--ease); }
.score-num { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.score-n { font-size: 30px; font-weight: 800; letter-spacing: -2px; line-height: 1; }
.score-d { font-size: 11px; color: var(--ink-5); }
.btn-pdf { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 7px 14px; border: 1px solid var(--rule); border-radius: 7px; background: var(--paper); color: var(--ink-3); text-decoration: none; cursor: pointer; transition: all .15s; }
.btn-pdf svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.btn-pdf:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-bg); }

/* BODY */
.rb { max-width: 1200px; margin: 0 auto; padding: 44px 24px 80px; }

/* CAT SCORES */
.cat-row { display: grid; grid-template-columns: repeat(6,1fr); border: 1px solid var(--rule); border-radius: 12px; overflow: hidden; margin-bottom: 48px; }
.cs { padding: 20px 16px; border-right: 1px solid var(--rule); transition: background .15s; }
.cs:last-child { border-right: none; }
.cs:hover { background: var(--paper-2); }
.cs-lbl { font-size: 11px; font-weight: 500; color: var(--ink-5); margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-n { font-size: 24px; font-weight: 800; letter-spacing: -1.5px; line-height: 1; margin-bottom: 8px; }
.cs-bar { height: 3px; background: var(--paper-3); border-radius: 2px; overflow: hidden; }
.cs-fill { height: 100%; border-radius: 2px; transition: width 1s var(--ease); }

/* AI SUMMARY SECTION */
.ai-summary {
    background: var(--paper-2);
    border: 1px solid var(--rule);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 48px;
}
.ai-summary-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.ai-summary-icon { width: 32px; height: 32px; background: var(--blue-bg); border: 1px solid var(--blue-bd); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ai-summary-icon svg { width: 14px; height: 14px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.ai-summary-title { font-size: 15px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); }
.ai-summary-sub { font-size: 12px; color: var(--ink-5); margin-top: 2px; }
.ai-summary-body { font-size: 14px; color: var(--ink-3); line-height: 1.8; }
.ai-summary-body.loading { display: flex; align-items: center; gap: 10px; color: var(--ink-5); font-size: 13px; }
.ai-loading-spin { width: 16px; height: 16px; border: 2px solid var(--rule); border-top-color: var(--blue); border-radius: 50%; animation: spin-ai .8s linear infinite; flex-shrink: 0; }
@keyframes spin-ai { to { transform: rotate(360deg); } }

/* ISSUES */
.issue-group { margin-bottom: 44px; }
.ig-head { display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid var(--rule); margin-bottom: 10px; }
.ig-ico { width: 26px; height: 26px; background: var(--paper-3); border: 1px solid var(--rule); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.ig-ico svg { width: 12px; height: 12px; stroke: var(--ink-4); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.ig-name { font-size: 13px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); }
.ig-cnt { font-size: 11px; color: var(--ink-5); font-weight: 500; }
.ig-score { margin-left: auto; font-size: 13px; font-weight: 800; letter-spacing: -.5px; }
.issues { display: flex; flex-direction: column; gap: 5px; }
.issue { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid var(--rule); border-radius: 8px; background: var(--paper); transition: all .15s; position: relative; }
.issue.sev-critical { border-left: 3px solid var(--red); }
.issue.sev-warning  { border-left: 3px solid var(--amber); }
.issue.sev-info     { border-left: 3px solid var(--green); }
.issue:hover { border-color: var(--rule-2); box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.issue-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
.d-critical { background: var(--red); box-shadow: 0 0 5px rgba(239,68,68,.4); }
.d-warning  { background: var(--amber); }
.d-info     { background: var(--green); }
.issue-body { flex: 1; min-width: 0; }
.issue-title { font-size: 13px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); margin-bottom: 4px; }
.issue-desc  { font-size: 12px; color: var(--ink-5); line-height: 1.65; margin-bottom: 5px; }
.issue-fix { font-size: 12px; color: var(--blue); display: flex; gap: 5px; line-height: 1.55; }
.issue-fix-arr { flex-shrink: 0; }
.issue-url { font-size: 10px; color: var(--ink-5); background: var(--paper-3); border: 1px solid var(--rule); padding: 2px 7px; border-radius: 4px; font-family: var(--mono); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; align-self: flex-start; flex-shrink: 0; }

/* CTA */
.report-cta { background: var(--ink); border-radius: 20px; padding: 56px 48px; text-align: center; position: relative; overflow: hidden; margin-top: 48px; }
.report-cta::before { content: ''; position: absolute; top: -150px; left: 50%; transform: translateX(-50%); width: 500px; height: 350px; background: radial-gradient(ellipse at 50% 0%, rgba(45,145,206,.18) 0%, transparent 65%); pointer-events: none; }
.report-cta-inner { position: relative; z-index: 1; }
.report-cta h3 { font-size: 28px; font-weight: 800; letter-spacing: -1.5px; color: white; margin-bottom: 10px; }
.report-cta p  { font-size: 13px; color: rgba(255,255,255,.4); margin-bottom: 28px; }
.report-cta-btns { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; }

/* Colors */
.c-green { color: #16a34a; } .c-amber { color: #d97706; } .c-red { color: #dc2626; }
.b-green { background: #22c55e; } .b-amber { background: #f59e0b; } .b-red { background: #ef4444; }

@media (max-width: 1024px) { .cat-row { grid-template-columns: repeat(3,1fr); } .cs:nth-child(3n){border-right:none} .cs:nth-child(n+4){border-top:1px solid var(--rule)} }
@media (max-width: 768px) {
    .rh-inner { grid-template-columns: 1fr; gap: 24px; }
    .cat-row { grid-template-columns: repeat(2,1fr); }
    .cs:nth-child(2n){border-right:none} .cs:nth-child(3n){border-right:1px solid var(--rule)} .cs:nth-child(4n){border-right:none} .cs:nth-child(n+3){border-top:1px solid var(--rule)}
    .report-cta { padding: 40px 20px; }
    .issue-url { display: none; }
    .rb { padding: 32px 16px 60px; }
    .rh { padding: 32px 16px 28px; }
    .ai-summary { padding: 24px 18px; }
}
@media (max-width: 480px) { .cat-row { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')

@php
use Illuminate\Support\Str;
$score = $audit->score_total ?? 0;
$circ = 314;
$offset = $circ - ($score / 100 * $circ);
$scoreColor = $score >= 80 ? '#16a34a' : ($score >= 50 ? '#d97706' : '#dc2626');
$categories = [
    'technical' => ['label'=>'Tehnic','score'=>$audit->score_technical??0,'icon'=>'<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'],
    'seo'       => ['label'=>'SEO','score'=>$audit->score_seo??0,'icon'=>'<circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>'],
    'legal'     => ['label'=>'Legal','score'=>$audit->score_legal??0,'icon'=>'<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    'eeeat'     => ['label'=>'E-E-A-T','score'=>$audit->score_eeeat??0,'icon'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
    'content'   => ['label'=>'Continut','score'=>$audit->score_content??0,'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
    'ux'        => ['label'=>'UX','score'=>$audit->score_ux??0,'icon'=>'<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'],
];
@endphp

{{-- HEADER --}}
<div class="rh">
    <div class="rh-inner">
        <div>
            <div class="rh-bread"><a href="{{ route('home') }}">Inovex Audit</a><span>/</span><span>Raport</span></div>
            <h1 class="rh-url">{{ $audit->url }}</h1>
            <div class="rh-tags">
                @if($critical>0)<span class="chip chip-red">{{ $critical }} {{ $critical===1?'problema critica':'probleme critice' }}</span>@endif
                @if($warnings>0)<span class="chip chip-amber">{{ $warnings }} {{ $warnings===1?'avertisment':'avertismente' }}</span>@endif
                @if($info>0)<span class="chip chip-green">{{ $info }} {{ $info===1?'observatie':'observatii' }}</span>@endif
            </div>
            <div class="rh-meta">
                <span>Generat: {{ $audit->completed_at ? $audit->completed_at->format('d.m.Y, H:i') : 'acum' }}</span>
                <div class="rh-meta-dot"></div><span>Disponibil 30 de zile</span>
                <div class="rh-meta-dot"></div><span>{{ $audit->email }}</span>
            </div>
        </div>
        <div class="score-w">
            <div class="score-lbl">Scor general</div>
            <div class="score-ring">
                <svg viewBox="0 0 110 110">
                    <circle class="score-track" cx="55" cy="55" r="50"/>
                    <circle class="score-fill" cx="55" cy="55" r="50" stroke="{{ $scoreColor }}" stroke-dashoffset="{{ number_format($offset,2) }}"/>
                </svg>
                <div class="score-num">
                    <span class="score-n" style="color:{{ $scoreColor }}">{{ $score }}</span>
                    <span class="score-d">/100</span>
                </div>
            </div>
            <a href="{{ route('audit.pdf', $audit->public_token) }}" class="btn-pdf">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descarca PDF
            </a>
        </div>
    </div>
</div>

{{-- BODY --}}
<div class="rb">
    {{-- Category scores --}}
    <div class="cat-row" data-reveal>
        @foreach($categories as $key => $cat)
            @php $s=$cat['score']; $cc=$s>=80?'c-green':($s>=50?'c-amber':'c-red'); $bc=$s>=80?'b-green':($s>=50?'b-amber':'b-red'); @endphp
            <div class="cs">
                <div class="cs-lbl">{{ $cat['label'] }}</div>
                <div class="cs-n {{ $cc }}">{{ $s }}</div>
                <div class="cs-bar"><div class="cs-fill {{ $bc }}" style="width:{{ $s }}%"></div></div>
            </div>
        @endforeach
    </div>

    {{-- AI SUMMARY --}}
    <div class="ai-summary" data-reveal>
        <div class="ai-summary-header">
            <div class="ai-summary-icon">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div class="ai-summary-title">Rezumat AI si plan de actiune</div>
                <div class="ai-summary-sub">Analiza generata automat pe baza problemelor identificate</div>
            </div>
        </div>
        <div class="ai-summary-body loading" id="aiSummaryBody">
            <div class="ai-loading-spin"></div>
            <span>Se genereaza rezumatul...</span>
        </div>
    </div>

    {{-- Issues per category --}}
    @foreach($categories as $key => $cat)
        @if(isset($issuesByCategory[$key]) && $issuesByCategory[$key]->count() > 0)
            @php $s=$cat['score']; $cc=$s>=80?'c-green':($s>=50?'c-amber':'c-red'); @endphp
            <div class="issue-group" data-reveal>
                <div class="ig-head">
                    <div class="ig-ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">{!! $cat['icon'] !!}</svg></div>
                    <span class="ig-name">{{ $cat['label'] }}</span>
                    <span class="ig-cnt">{{ $issuesByCategory[$key]->count() }} probleme</span>
                    <span class="ig-score {{ $cc }}">{{ $s }}/100</span>
                </div>
                <div class="issues">
                    @foreach($issuesByCategory[$key]->sortBy(fn($i)=>match($i->severity){'critical'=>0,'warning'=>1,default=>2}) as $issue)
                        <div class="issue sev-{{ $issue->severity }}">
                            <div class="issue-dot d-{{ $issue->severity }}"></div>
                            <div class="issue-body">
                                <div class="issue-title">{{ $issue->title }}</div>
                                <div class="issue-desc">{{ $issue->description }}</div>
                                @if($issue->suggestion)<div class="issue-fix"><span class="issue-fix-arr">→</span><span>{{ $issue->suggestion }}</span></div>@endif
                            </div>
                            @if($issue->affected_url)<div class="issue-url">{{ Str::limit($issue->affected_url, 30) }}</div>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    {{-- CTA --}}
    <div class="report-cta" data-reveal>
        <div class="report-cta-inner">
            <h3>Vrei sa rezolvam noi toate problemele?</h3>
            <p>Echipa Inovex.ro implementeaza toate imbunatatirile in 5-7 zile lucratoare, cu garantie.</p>
            <div class="report-cta-btns">
                <a href="https://inovex.ro/contact" target="_blank" class="btn btn-on-dark btn-lg">
                    Contacteaza Inovex.ro
                    <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="tel:+40750456096" class="btn btn-ghost-on-dark btn-lg">0750 456 096</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Load AI summary via API
(function(){
    const auditId = {{ $audit->id }};
    const el = document.getElementById('aiSummaryBody');

    fetch('/api/audit/' + auditId + '/summary')
        .then(r => r.json())
        .then(d => {
            el.classList.remove('loading');
            if (d.summary) {
                el.innerHTML = d.summary.replace(/\n\n/g, '</p><p style="margin-top:12px">').replace(/\n/g, '<br>');
                el.innerHTML = '<p>' + el.innerHTML + '</p>';
            } else {
                el.textContent = 'Rezumatul nu este disponibil momentan.';
            }
        })
        .catch(() => {
            el.classList.remove('loading');
            el.textContent = 'Nu s-a putut incarca rezumatul. Reincarca pagina pentru a incerca din nou.';
        });
})();
</script>
@endpush