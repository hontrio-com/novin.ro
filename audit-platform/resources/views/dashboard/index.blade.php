@extends('layouts.dashboard')
@section('title','Dashboard')
@section('page_title','Dashboard')

@push('styles')
<style>
/* ── STATS GRID ─────────────────────────────────────────── */
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; }
.stat-card  { background: var(--paper); border: 1px solid var(--rule); border-radius: 12px; padding: 18px 20px; }
.stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; color: var(--ink-5); margin-bottom: 8px; }
.stat-val   { font-size: 28px; font-weight: 800; letter-spacing: -1.5px; color: var(--ink); line-height: 1; }
.stat-val.red   { color: var(--red); }
.stat-val.green { color: var(--green); }
.stat-val.sm    { font-size: 16px; letter-spacing: -.5px; margin-top: 4px; }
.stat-sub   { font-size: 11px; color: var(--ink-5); margin-top: 6px; }
.stat-delta { display: inline-flex; align-items: center; gap: 3px; font-size: 11px; font-weight: 600; margin-top: 6px; padding: 2px 7px; border-radius: 4px; }
.stat-delta.up   { background: var(--green-bg); color: var(--green); }
.stat-delta.down { background: var(--red-bg);   color: var(--red);   }
.stat-delta.flat { background: var(--paper-3);  color: var(--ink-5); }
/* ── MAIN GRID ──────────────────────────────────────────── */
.dash-grid { display: grid; grid-template-columns: 1fr 340px; gap: 16px; margin-bottom: 20px; }
/* ── CARD BASE ──────────────────────────────────────────── */
.db-card { background: var(--paper); border: 1px solid var(--rule); border-radius: 12px; overflow: hidden; }
.db-card-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--rule); }
.db-card-title { font-size: 13px; font-weight: 700; color: var(--ink); letter-spacing: -.2px; }
.db-card-sub   { font-size: 11px; color: var(--ink-5); margin-top: 2px; }
.db-card-body  { padding: 20px; }
/* ── TREND CHART ────────────────────────────────────────── */
.chart-wrap { position: relative; }
.chart-svg  { width: 100%; height: 180px; display: block; overflow: visible; }
.chart-tooltip { position: absolute; background: var(--ink); color: white; font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 6px; pointer-events: none; opacity: 0; transition: opacity .15s; white-space: nowrap; transform: translate(-50%, -130%); }
.chart-empty { text-align: center; padding: 48px 20px; color: var(--ink-5); font-size: 13px; }
/* ── COMPARE ────────────────────────────────────────────── */
.compare-row { display: flex; align-items: center; gap: 10px; padding: 10px 20px; border-bottom: 1px solid var(--rule); }
.compare-row:last-child { border-bottom: none; }
.compare-label { font-size: 11px; font-weight: 600; color: var(--ink-4); width: 68px; flex-shrink: 0; }
.compare-bars  { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.compare-bar-wrap { position: relative; height: 8px; background: var(--paper-3); border-radius: 4px; overflow: hidden; }
.compare-bar { height: 100%; border-radius: 4px; transition: width .4s cubic-bezier(.16,1,.3,1); }
.compare-vals { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.compare-val  { font-size: 12px; font-weight: 700; width: 26px; text-align: right; }
.compare-delta { font-size: 10px; font-weight: 700; min-width: 30px; }
.delta-up   { color: var(--green); }
.delta-down { color: var(--red);   }
.delta-flat { color: var(--ink-6); }
.compare-legend { display: flex; gap: 16px; padding: 12px 20px; border-bottom: 1px solid var(--rule); background: var(--paper-2); }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--ink-5); }
.legend-dot  { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
/* ── QUICK WINS MINI ────────────────────────────────────── */
.qw-mini { padding: 14px 20px; border-bottom: 1px solid var(--rule); display: flex; align-items: flex-start; gap: 10px; }
.qw-mini:last-child { border-bottom: none; }
.qw-mini-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.qw-mini-dot.critical { background: var(--red); box-shadow: 0 0 5px rgba(239,68,68,.3); }
.qw-mini-dot.warning  { background: var(--amber); }
.qw-mini-body  { flex: 1; min-width: 0; }
.qw-mini-title { font-size: 12px; font-weight: 700; color: var(--ink); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.qw-mini-desc  { font-size: 11px; color: var(--ink-5); line-height: 1.5; }
/* ── AUDIT TABLE ────────────────────────────────────────── */
.audit-tbl-card { background: var(--paper); border: 1px solid var(--rule); border-radius: 12px; overflow: hidden; }
.audit-tbl-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--rule); }
.tbl-wrap { overflow-x: auto; }
.tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.tbl th { text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; color: var(--ink-5); border-bottom: 1px solid var(--rule); background: var(--paper-2); white-space: nowrap; }
.tbl td { padding: 13px 16px; border-bottom: 1px solid var(--rule); vertical-align: middle; }
.tbl tr:last-child td { border-bottom: none; }
.tbl tr:hover td { background: var(--paper-2); }
.tbl-url   { font-size: 12px; font-weight: 600; color: var(--ink-3); max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-email { font-size: 11px; color: var(--ink-5); margin-top: 2px; }
.score-badge { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; font-size: 12px; font-weight: 800; }
.score-badge.g { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-bd); }
.score-badge.a { background: var(--amber-bg); color: var(--amber); border: 1px solid var(--amber-bd); }
.score-badge.r { background: var(--red-bg);   color: var(--red);   border: 1px solid var(--red-bd); }
.chip   { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; }
.chip.r { background: var(--red-bg);   color: var(--red);   }
.chip.a { background: var(--amber-bg); color: var(--amber); }
.chip.g { background: var(--green-bg); color: var(--green); }
.chips  { display: flex; gap: 4px; flex-wrap: wrap; }
.badge   { display: inline-block; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
.badge.g { background: var(--green-bg); color: var(--green); }
.badge.a { background: var(--amber-bg); color: var(--amber); }
.badge.r { background: var(--red-bg);   color: var(--red);   }
.tbl-action { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 5px 12px; border: 1px solid var(--rule); border-radius: 6px; background: var(--paper); color: var(--ink-3); text-decoration: none; transition: all .15s; white-space: nowrap; }
.tbl-action:hover { background: var(--ink); color: white; border-color: var(--ink); }
.tbl-action svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
/* ── EMPTY ──────────────────────────────────────────────── */
.empty { text-align: center; padding: 56px 24px; }
.empty-icon { width: 52px; height: 52px; background: var(--paper-3); border: 1px solid var(--rule); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.empty-icon svg { width: 22px; height: 22px; stroke: var(--ink-5); fill: none; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
.empty-title { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.empty-desc  { font-size: 13px; color: var(--ink-5); max-width: 320px; margin: 0 auto 20px; line-height: 1.6; }
/* ── BUTTONS ────────────────────────────────────────────── */
.btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; text-decoration: none; transition: all .15s; cursor: pointer; border: none; }
.btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.btn-dark    { background: var(--ink); color: white; }
.btn-dark:hover { background: #222; }
.btn-sm      { font-size: 12px; padding: 6px 12px; }
.btn-lg      { padding: 12px 24px; font-size: 14px; }
.btn-outline { background: transparent; border: 1px solid var(--rule); color: var(--ink-3); }
.btn-outline:hover { border-color: var(--ink-4); color: var(--ink); }
/* ── RESPONSIVE ─────────────────────────────────────────── */
@media (max-width: 1100px) { .dash-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px)  { .stats-grid { grid-template-columns: repeat(2,1fr); } }
</style>
@endpush

@section('content')

@php
$completed  = $audits->where('status','completed');
$scoreColor = fn($s) => $s >= 80 ? 'var(--green)' : ($s >= 50 ? 'var(--amber)' : 'var(--red)');
$barColor   = fn($s) => $s >= 80 ? '#22c55e'       : ($s >= 50 ? '#f59e0b'      : '#ef4444');
@endphp

{{-- ── STATS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Audituri totale</div>
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-sub">{{ $completed->count() }} finalizate</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Scor mediu</div>
        <div class="stat-val" style="color:{{ $scoreColor($stats['avg_score']) }}">{{ $stats['avg_score'] }}<span style="font-size:14px;font-weight:400;color:var(--ink-5)">/100</span></div>
        @if($stats['improved'] !== null)
            @php $imp = $stats['improved']; @endphp
            <div class="stat-delta {{ $imp > 0 ? 'up' : ($imp < 0 ? 'down' : 'flat') }}">
                {{ $imp > 0 ? '↑' : ($imp < 0 ? '↓' : '→') }} {{ abs($imp) }} față de anterior
            </div>
        @else
            <div class="stat-sub">pe audituri completate</div>
        @endif
    </div>
    <div class="stat-card">
        <div class="stat-label">Probleme critice</div>
        <div class="stat-val {{ $stats['critical'] > 0 ? 'red' : 'green' }}">{{ $stats['critical'] }}</div>
        <div class="stat-sub">{{ $stats['critical'] > 0 ? 'necesită atenție urgentă' : 'nicio problemă critică' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ultimul audit</div>
        <div class="stat-val sm">{{ $stats['last_audit'] }}</div>
        <div class="stat-sub">Cel mai bun scor: <strong>{{ $stats['best_score'] }}/100</strong></div>
    </div>
</div>

{{-- ── MAIN GRID ── --}}
<div class="dash-grid">

    {{-- Stânga: Trend + Comparație --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Grafic trend --}}
        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Evoluție scoruri</div>
                    <div class="db-card-sub">Ultimele {{ $trend->count() }} audituri completate</div>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Audit nou
                </a>
            </div>
            <div class="db-card-body">
                @if($trend->count() < 2)
                    <div class="chart-empty">
                        <svg style="width:36px;height:36px;stroke:var(--ink-6);fill:none;stroke-width:1.5;stroke-linecap:round;margin:0 auto 12px;display:block" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        <p>Ai nevoie de cel puțin 2 audituri pentru a vedea evoluția</p>
                    </div>
                @else
                @php
                    $pts      = $trend;
                    $minScore = max(0, $pts->min('score') - 10);
                    $maxScore = min(100, $pts->max('score') + 5);
                    $range    = max($maxScore - $minScore, 20);
                    $svgW = 540; $svgH = 160; $padL = 32; $padR = 16; $padT = 12; $padB = 28;
                    $chartW = $svgW - $padL - $padR;
                    $chartH = $svgH - $padT - $padB;
                    $n = $pts->count();
                    $getX = fn($i) => $padL + ($n > 1 ? $i / ($n - 1) * $chartW : $chartW / 2);
                    $getY = fn($s) => $padT + $chartH - (($s - $minScore) / $range * $chartH);
                    $linePoints = $pts->map(fn($p,$i) => $getX($i) . ',' . $getY($p['score']))->implode(' ');
                    $areaPath = 'M ' . $getX(0) . ',' . $getY($pts[0]['score']);
                    foreach ($pts as $i => $p) { $areaPath .= ' L ' . $getX($i) . ',' . $getY($p['score']); }
                    $areaPath .= ' L ' . $getX($n-1) . ',' . ($padT+$chartH) . ' L ' . $getX(0) . ',' . ($padT+$chartH) . ' Z';
                @endphp
                <div class="chart-wrap" id="chartWrap">
                    <svg class="chart-svg" viewBox="0 0 {{ $svgW }} {{ $svgH }}" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#2D91CE" stop-opacity=".18"/>
                                <stop offset="100%" stop-color="#2D91CE" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        @foreach([0,25,50,75,100] as $tick)
                        @php $ty = $getY($tick); @endphp
                        @if($tick >= $minScore - 5 && $tick <= $maxScore + 5)
                        <line x1="{{ $padL }}" y1="{{ $ty }}" x2="{{ $svgW-$padR }}" y2="{{ $ty }}" stroke="#e5e5e5" stroke-width="1"/>
                        <text x="{{ $padL-4 }}" y="{{ $ty+4 }}" font-size="9" fill="#a3a3a3" text-anchor="end">{{ $tick }}</text>
                        @endif
                        @endforeach
                        <path d="{{ $areaPath }}" fill="url(#areaGrad)"/>
                        <polyline points="{{ $linePoints }}" fill="none" stroke="#2D91CE" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                        @foreach($pts as $i => $pt)
                        @php
                            $cx = $getX($i); $cy = $getY($pt['score']);
                            $dc = $pt['score'] >= 80 ? '#16a34a' : ($pt['score'] >= 50 ? '#d97706' : '#ef4444');
                        @endphp
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="5" fill="white" stroke="{{ $dc }}" stroke-width="2"
                                class="chart-dot" data-score="{{ $pt['score'] }}" data-date="{{ $pt['date'] }}" data-url="{{ $pt['url'] }}" style="cursor:pointer"/>
                        <text x="{{ $cx }}" y="{{ $padT+$chartH+18 }}" font-size="9" fill="#a3a3a3" text-anchor="middle">{{ $pt['date'] }}</text>
                        @endforeach
                    </svg>
                    <div class="chart-tooltip" id="chartTooltip"></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Comparație ultimele 2 audituri --}}
        @if($compare && $latest && $prev)
        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Comparație audituri</div>
                    <div class="db-card-sub">{{ $latest->created_at->format('d.m.Y') }} vs {{ $prev->created_at->format('d.m.Y') }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:13px;font-weight:800;color:{{ $scoreColor($latest->score_total ?? 0) }}">{{ $latest->score_total ?? '—' }}</span>
                    <span style="font-size:11px;color:var(--ink-6)">vs</span>
                    <span style="font-size:13px;font-weight:700;color:var(--ink-5)">{{ $prev->score_total ?? '—' }}</span>
                </div>
            </div>
            <div class="compare-legend">
                <div class="legend-item"><div class="legend-dot" style="background:#2D91CE"></div>Audit curent ({{ $latest->created_at->format('d.m') }})</div>
                <div class="legend-item"><div class="legend-dot" style="background:#e5e5e5;border:1px solid #d4d4d4"></div>Anterior ({{ $prev->created_at->format('d.m') }})</div>
            </div>
            @foreach($compare as $cat)
            @php
                $delta = $cat['delta'];
                $dCls  = $delta > 0 ? 'delta-up' : ($delta < 0 ? 'delta-down' : 'delta-flat');
                $dStr  = $delta > 0 ? "+{$delta}" : ($delta < 0 ? "{$delta}" : '=');
            @endphp
            <div class="compare-row">
                <div class="compare-label">{{ $cat['label'] }}</div>
                <div class="compare-bars">
                    <div class="compare-bar-wrap"><div class="compare-bar" style="width:{{ max(2,$cat['prev']) }}%;background:var(--rule-2);"></div></div>
                    <div class="compare-bar-wrap"><div class="compare-bar" style="width:{{ max(2,$cat['latest']) }}%;background:{{ $barColor($cat['latest']) }};"></div></div>
                </div>
                <div class="compare-vals">
                    <div class="compare-val" style="color:{{ $scoreColor($cat['latest']) }}">{{ $cat['latest'] }}</div>
                    <div class="compare-delta {{ $dCls }}">{{ $dStr }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- Dreapta: Quick Wins + Ultimul audit --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        @if($latest && $quickWins->count() > 0)
        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">⚡ Quick Wins</div>
                    <div class="db-card-sub">Din ultimul audit</div>
                </div>
                <a href="{{ route('audit.report', $latest->public_token) }}" class="btn btn-outline btn-sm">Toate →</a>
            </div>
            @foreach($quickWins as $qw)
            @php $issue = $qw['issue']; @endphp
            <div class="qw-mini">
                <div class="qw-mini-dot {{ $issue->severity }}"></div>
                <div class="qw-mini-body">
                    <div class="qw-mini-title" title="{{ $issue->title }}">{{ $issue->title }}</div>
                    <div class="qw-mini-desc">{{ \Illuminate\Support\Str::limit($issue->description, 85) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($latest)
        @php
            $sc  = $latest->score_total ?? 0;
            $scC = $sc >= 80 ? 'g' : ($sc >= 50 ? 'a' : 'r');
            $cr  = $latest->issues->where('severity','critical')->count();
            $wa  = $latest->issues->where('severity','warning')->count();
        @endphp
        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Ultimul audit</div>
                    <div class="db-card-sub">{{ $latest->created_at->diffForHumans() }}</div>
                </div>
                <div class="score-badge {{ $scC }}">{{ $sc }}</div>
            </div>
            <div class="db-card-body" style="padding:16px 20px;">
                <p style="font-size:12px;font-weight:600;color:var(--ink-3);margin-bottom:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $latest->url }}">{{ $latest->url }}</p>
                <div class="chips" style="margin-bottom:14px;">
                    @if($cr > 0)<span class="chip r">{{ $cr }} critice</span>@endif
                    @if($wa > 0)<span class="chip a">{{ $wa }} avertismente</span>@endif
                    @if($cr === 0 && $wa === 0)<span class="chip g">Fără probleme majore</span>@endif
                </div>
                @foreach(['Tehnic'=>$latest->score_technical??0,'SEO'=>$latest->score_seo??0,'Legal'=>$latest->score_legal??0,'UX'=>$latest->score_ux??0] as $lbl => $sv)
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <span style="font-size:10px;font-weight:600;color:var(--ink-5);width:40px;flex-shrink:0;">{{ $lbl }}</span>
                    <div style="flex:1;height:5px;background:var(--paper-3);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:{{ $sv }}%;background:{{ $barColor($sv) }};border-radius:3px;"></div>
                    </div>
                    <span style="font-size:10px;font-weight:700;color:{{ $scoreColor($sv) }};width:22px;text-align:right;">{{ $sv }}</span>
                </div>
                @endforeach
                <a href="{{ route('audit.report', $latest->public_token) }}" class="btn btn-dark btn-sm" style="width:100%;justify-content:center;margin-top:14px;">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Vezi raportul complet
                </a>
            </div>
        </div>
        @else
        <div class="db-card">
            <div class="empty">
                <div class="empty-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg></div>
                <div class="empty-title">Niciun audit efectuat</div>
                <div class="empty-desc">Pornește primul audit pentru a vedea statistici.</div>
                <a href="{{ route('home') }}" class="btn btn-dark btn-lg">Pornește primul audit <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ── TABEL TOATE AUDITURILE ── --}}
<div class="audit-tbl-card">
    <div class="audit-tbl-head">
        <div>
            <div class="db-card-title">Toate auditurile</div>
            <div class="db-card-sub">{{ $audits->count() }} în total</div>
        </div>
        <a href="{{ route('home') }}" class="btn btn-dark btn-sm">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Audit nou
        </a>
    </div>
    @if($audits->isEmpty())
        <div class="empty">
            <div class="empty-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg></div>
            <div class="empty-title">Niciun audit efectuat încă</div>
            <div class="empty-desc">Pornește primul audit pentru a vedea rezultatele și recomandările.</div>
            <a href="{{ route('home') }}" class="btn btn-dark btn-lg">Pornește primul audit <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        </div>
    @else
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Website</th><th>Scor</th><th>Probleme</th><th>Status</th><th>Data</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $a)
                    @php
                        $s2  = $a->score_total;
                        $sC  = $s2 >= 80 ? 'g' : ($s2 >= 50 ? 'a' : 'r');
                        $cr2 = $a->issues->where('severity','critical')->count();
                        $wa2 = $a->issues->where('severity','warning')->count();
                        $stM = ['completed'=>['g','Finalizat'],'pending'=>['a','În așteptare'],'processing'=>['a','În procesare'],'failed'=>['r','Eroare']];
                        $st  = $stM[$a->status] ?? ['a', ucfirst($a->status)];
                        $isL = $latest && $a->id === $latest->id;
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($isL)<span style="font-size:9px;font-weight:700;background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-bd);padding:1px 5px;border-radius:3px;flex-shrink:0;">RECENT</span>@endif
                                <div>
                                    <div class="tbl-url" title="{{ $a->url }}">{{ $a->url }}</div>
                                    <div class="tbl-email">{{ $a->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($s2 !== null)<div class="score-badge {{ $sC }}">{{ $s2 }}</div>
                            @else<span style="color:var(--ink-6)">—</span>@endif
                        </td>
                        <td>
                            @if($a->status === 'completed')
                                <div class="chips">
                                    @if($cr2 > 0)<span class="chip r">{{ $cr2 }} critice</span>@endif
                                    @if($wa2 > 0)<span class="chip a">{{ $wa2 }} avert.</span>@endif
                                    @if($cr2 === 0 && $wa2 === 0)<span class="chip g">OK</span>@endif
                                </div>
                            @else<span style="color:var(--ink-6);font-size:12px">—</span>@endif
                        </td>
                        <td><span class="badge {{ $st[0] }}">{{ $st[1] }}</span></td>
                        <td style="font-size:12px;color:var(--ink-5);white-space:nowrap;">
                            {{ $a->created_at->format('d.m.Y') }}
                            <span style="display:block;font-size:11px;">{{ $a->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            @if($a->status === 'completed' && $a->public_token)
                                <a href="{{ route('audit.report', $a->public_token) }}" class="tbl-action">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Vezi raport
                                </a>
                            @elseif(in_array($a->status, ['processing','pending']))
                                <a href="{{ route('audit.progress', $a->id) }}" class="tbl-action" style="color:var(--ink-4);">
                                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-6.59"/></svg>
                                    În curs
                                </a>
                            @else<span style="color:var(--ink-6);font-size:12px">—</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const dots    = document.querySelectorAll('.chart-dot');
const tooltip = document.getElementById('chartTooltip');
const wrap    = document.getElementById('chartWrap');
if (dots.length && tooltip && wrap) {
    dots.forEach(dot => {
        dot.addEventListener('mouseenter', function() {
            const url = this.getAttribute('data-url').replace(/^https?:\/\//, '').substring(0, 28);
            tooltip.textContent = this.getAttribute('data-date') + ' · ' + url + ' · ' + this.getAttribute('data-score') + '/100';
            const wr = wrap.getBoundingClientRect();
            const dr = this.getBoundingClientRect();
            tooltip.style.left    = (dr.left - wr.left + dr.width / 2) + 'px';
            tooltip.style.top     = (dr.top  - wr.top) + 'px';
            tooltip.style.opacity = '1';
        });
        dot.addEventListener('mouseleave', () => { tooltip.style.opacity = '0'; });
    });
}
</script>
@endpush