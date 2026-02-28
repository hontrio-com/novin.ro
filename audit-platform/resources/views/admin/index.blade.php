@extends('layouts.admin')
@section('title','Overview')
@section('page_title','Overview')

@push('styles')
<style>
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.stat-card{background:var(--paper);border:1px solid var(--rule);border-radius:12px;padding:18px 20px;}
.stat-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-5);margin-bottom:8px;}
.stat-val{font-size:30px;font-weight:800;letter-spacing:-1.5px;color:var(--ink);line-height:1;}
.stat-val.blue{color:var(--blue);}
.stat-val.green{color:var(--green);}
.stat-val.red{color:var(--red);}
.stat-sub{font-size:11px;color:var(--ink-5);margin-top:6px;}

.grid-2{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:16px;}

.card{background:var(--paper);border:1px solid var(--rule);border-radius:12px;overflow:hidden;}
.card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--rule);}
.card-title{font-size:13px;font-weight:700;color:var(--ink);letter-spacing:-.2px;}
.card-sub{font-size:11px;color:var(--ink-5);margin-top:2px;}
.card-body{padding:20px;}

/* Mini bar chart */
.bar-chart{display:flex;align-items:flex-end;gap:3px;height:80px;}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;}
.bar-fill{width:100%;border-radius:3px 3px 0 0;background:var(--blue);opacity:.7;transition:opacity .15s;min-height:2px;}
.bar-fill:hover{opacity:1;}
.bar-label{font-size:8px;color:var(--ink-6);writing-mode:vertical-lr;transform:rotate(180deg);height:24px;overflow:hidden;}

/* Score dist */
.dist-row{display:flex;align-items:center;gap:10px;padding:10px 20px;border-bottom:1px solid var(--rule);}
.dist-row:last-child{border-bottom:none;}
.dist-label{font-size:12px;font-weight:600;color:var(--ink-3);width:50px;flex-shrink:0;}
.dist-bar-wrap{flex:1;height:10px;background:var(--paper-3);border-radius:5px;overflow:hidden;}
.dist-bar{height:100%;border-radius:5px;}
.dist-count{font-size:12px;font-weight:700;color:var(--ink-4);width:28px;text-align:right;}

/* Recent audits table */
.tbl{width:100%;border-collapse:collapse;font-size:12px;}
.tbl th{text-align:left;padding:9px 16px;font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-5);border-bottom:1px solid var(--rule);background:var(--paper-2);}
.tbl td{padding:12px 16px;border-bottom:1px solid var(--rule);vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:var(--paper-2);}
.tbl-url{font-size:12px;font-weight:600;color:var(--ink-3);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.badge{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;}
.badge.g{background:var(--green-bg);color:var(--green);}
.badge.a{background:var(--amber-bg);color:var(--amber);}
.badge.r{background:var(--red-bg);color:var(--red);}
.score-chip{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;font-size:11px;font-weight:800;}
.score-chip.g{background:var(--green-bg);color:var(--green);border:1px solid var(--green-bd);}
.score-chip.a{background:var(--amber-bg);color:var(--amber);border:1px solid var(--amber-bd);}
.score-chip.r{background:var(--red-bg);color:var(--red);border:1px solid var(--red-bd);}

.btn{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:7px 14px;border-radius:7px;text-decoration:none;transition:all .15s;border:1px solid var(--rule);background:var(--paper);color:var(--ink-3);}
.btn:hover{border-color:var(--ink-4);color:var(--ink);}
.btn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.btn-dark{background:var(--ink);color:white;border-color:var(--ink);}
.btn-dark:hover{background:#222;border-color:#222;}

@media(max-width:1100px){.grid-2{grid-template-columns:1fr;}}
@media(max-width:768px){
    .stats-grid{grid-template-columns:repeat(2,1fr);}
    .fin-grid{grid-template-columns:repeat(2,1fr)!important;}
    .tbl th:nth-child(2),.tbl td:nth-child(2){display:none;}
    .tbl th:nth-child(5),.tbl td:nth-child(5){display:none;}
    .tbl th:nth-child(6),.tbl td:nth-child(6){display:none;}
}
@media(max-width:480px){
    .stats-grid{grid-template-columns:1fr;}
    .fin-grid{grid-template-columns:1fr!important;}
    .tbl th:nth-child(3),.tbl td:nth-child(3){display:none;}
}
/* Financiar */
.fin-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.fin-card{background:var(--paper);border:1px solid var(--rule);border-radius:12px;padding:18px 20px;position:relative;overflow:hidden;}
.fin-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--green);}
.fin-card.blue::before{background:var(--blue);}
.fin-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-5);margin-bottom:8px;}
.fin-val{font-size:24px;font-weight:800;letter-spacing:-1px;color:var(--ink);line-height:1;}
.fin-currency{font-size:14px;font-weight:600;color:var(--ink-4);margin-left:3px;}
.fin-sub{font-size:11px;color:var(--ink-5);margin-top:6px;}
.fin-delta{display:inline-flex;align-items:center;gap:3px;font-size:11px;font-weight:700;margin-top:6px;padding:2px 7px;border-radius:4px;}
.fin-delta.up{background:var(--green-bg);color:var(--green);}
.fin-delta.down{background:var(--red-bg);color:var(--red);}
/* Bar chart venituri */
.rev-bars{display:flex;align-items:flex-end;gap:6px;height:70px;margin-top:4px;}
.rev-bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;}
.rev-bar-fill{width:100%;border-radius:4px 4px 0 0;background:var(--green);opacity:.7;min-height:3px;transition:opacity .15s;}
.rev-bar-fill:hover{opacity:1;}
.rev-bar-lbl{font-size:8px;color:var(--ink-6);white-space:nowrap;}
/* Payments table */
.pay-row{display:flex;align-items:center;gap:10px;padding:10px 20px;border-bottom:1px solid var(--rule);}
.pay-row:last-child{border-bottom:none;}
.pay-url{font-size:12px;font-weight:600;color:var(--ink-3);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.pay-amount{font-size:13px;font-weight:800;color:var(--green);flex-shrink:0;}
.pay-date{font-size:11px;color:var(--ink-5);flex-shrink:0;white-space:nowrap;}
</style>
@endpush

@section('content')

@php
$totalAudits    = $totalAudits ?? 0;
$completedAudits= $completedAudits ?? 0;
$totalUsers     = $totalUsers ?? 0;
$avgScore       = $avgScore ?? 0;
$totalRevenue       = $totalRevenue ?? 0;
$revenueThisMonth   = $revenueThisMonth ?? 0;
$revenueLastMonth   = $revenueLastMonth ?? 0;
$revenueGrowth      = $revenueGrowth ?? null;
$paymentsThisMonth  = $paymentsThisMonth ?? 0;
$revenueByMonth     = $revenueByMonth ?? collect();
$recentPayments     = $recentPayments ?? collect();
$scoreColor     = fn($s) => $s >= 80 ? 'var(--green)' : ($s >= 50 ? 'var(--amber)' : 'var(--red)');
$barColor       = fn($s) => $s >= 80 ? '#22c55e' : ($s >= 50 ? '#f59e0b' : '#ef4444');
$maxDay = $last30->max() ?: 1;
$totalDist = array_sum(is_array($scoreDist) ? $scoreDist : $scoreDist->toArray());
$maxRev = $revenueByMonth->max() ?: 1;
@endphp

<!-- ══ FINANCIAR ═══════════════════════════════════════════ -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--ink-4);">Financiar</span>
</div>

<div class="fin-grid">
    {{-- Venit luna aceasta --}}
    <div class="fin-card">
        <div class="fin-label">Venit luna aceasta</div>
        <div class="fin-val">{{ number_format($revenueThisMonth, 0, ',', '.') }}<span class="fin-currency">RON</span></div>
        @if($revenueGrowth !== null)
            <div class="fin-delta {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
                {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}% față de luna trecută
            </div>
        @else
            <div class="fin-sub">Prima lună cu date</div>
        @endif
        <div class="fin-sub" style="margin-top:4px;">{{ $paymentsThisMonth }} plăți procesate</div>
    </div>

    {{-- Venit luna trecuta --}}
    <div class="fin-card blue">
        <div class="fin-label">Venit luna trecută</div>
        <div class="fin-val">{{ number_format($revenueLastMonth, 0, ',', '.') }}<span class="fin-currency">RON</span></div>
        <div class="fin-sub">Luna {{ now()->subMonth()->format('F Y') }}</div>
    </div>

    {{-- Venit total --}}
    <div class="fin-card">
        <div class="fin-label">Venit total</div>
        <div class="fin-val">{{ number_format($totalRevenue, 0, ',', '.') }}<span class="fin-currency">RON</span></div>
        <div class="fin-sub">Din toate plățile procesate</div>
    </div>

    {{-- Valoare medie per audit --}}
    <div class="fin-card blue">
        <div class="fin-label">Valoare medie</div>
        @php $paidCount = \App\Models\Payment::where('status','paid')->count(); @endphp
        <div class="fin-val">{{ $paidCount > 0 ? number_format($totalRevenue / $paidCount, 0, ',', '.') : 0 }}<span class="fin-currency">RON</span></div>
        <div class="fin-sub">per audit plătit · {{ $paidCount }} total plăți</div>
    </div>
</div>

{{-- Grafic venituri pe 6 luni + ultimele plăți --}}
<div class="grid-2" style="margin-bottom:24px;">
    <div class="card">
        <div class="card-head">
            <div>
                <div class="card-title">Venituri lunare</div>
                <div class="card-sub">Ultimele 6 luni</div>
            </div>
        </div>
        <div class="card-body">
            <div class="rev-bars">
                @foreach($revenueByMonth as $month => $rev)
                @php $pct = $maxRev > 0 ? max(($rev / $maxRev) * 100, $rev > 0 ? 4 : 0) : 0; @endphp
                <div class="rev-bar-col">
                    <div class="rev-bar-fill" style="height:{{ $pct }}%;"></div>
                    <div class="rev-bar-lbl">{{ $month }}</div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:1px solid var(--rule);">
                <span style="font-size:11px;color:var(--ink-5);">Total 6 luni:</span>
                <span style="font-size:13px;font-weight:800;color:var(--green);">{{ number_format($revenueByMonth->sum(), 0, ',', '.') }} RON</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <div class="card-title">Ultimele plăți</div>
                <div class="card-sub">Cele mai recente tranzacții</div>
            </div>
        </div>
        @if($recentPayments->isEmpty())
            <div style="padding:32px 20px;text-align:center;color:var(--ink-5);font-size:13px;">Nicio plată încă</div>
        @else
            @foreach($recentPayments as $pay)
            <div class="pay-row">
                <div class="pay-url" title="{{ $pay->audit?->url ?? '—' }}">{{ $pay->audit?->url ?? '—' }}</div>
                <div class="pay-amount">{{ number_format($pay->amount / 100, 0, ',', '.') }} RON</div>
                <div class="pay-date">{{ $pay->paid_at?->format('d.m.Y') ?? '—' }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>

<!-- ══ AUDITURI ═══════════════════════════════════════════ -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--ink-4);">Audituri & Utilizatori</span>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total audituri</div>
        <div class="stat-val blue">{{ $totalAudits }}</div>
        <div class="stat-sub">{{ $completedAudits }} finalizate · {{ $failedAudits }} eșuate</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Utilizatori</div>
        <div class="stat-val">{{ $totalUsers }}</div>
        <div class="stat-sub">+{{ $newUsers }} în ultima săptămână</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Scor mediu</div>
        <div class="stat-val" style="color:{{ $scoreColor($avgScore) }}">{{ $avgScore }}</div>
        <div class="stat-sub">pe audituri completate</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rată succes</div>
        <div class="stat-val green">{{ $totalAudits > 0 ? round($completedAudits / $totalAudits * 100) : 0 }}<span style="font-size:16px;font-weight:400;color:var(--ink-5)">%</span></div>
        <div class="stat-sub">audituri finalizate cu succes</div>
    </div>
</div>

<div class="grid-2">
    <!-- Audituri pe zi — ultimele 30 zile -->
    <div class="card">
        <div class="card-head">
            <div>
                <div class="card-title">Audituri pe zi</div>
                <div class="card-sub">Ultimele 30 de zile</div>
            </div>
            <a href="{{ route('admin.audits') }}" class="btn">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Toate auditurile
            </a>
        </div>
        <div class="card-body">
            @php
                $pts   = $last30->values();
                $svgW  = 520; $svgH = 100; $pad = 8;
                $cW    = $svgW - $pad*2;
                $n     = $pts->count();
                $maxV  = max($pts->max(), 1);
                $getX  = fn($i) => $pad + ($n > 1 ? $i / ($n-1) * $cW : $cW/2);
                $getY  = fn($v) => $svgH - $pad - ($v / $maxV * ($svgH - $pad*2));
                $linePts = $pts->map(fn($v,$i) => $getX($i).','.$getY($v))->implode(' ');
                $area  = 'M '.$getX(0).','.$getY($pts[0]);
                foreach ($pts as $i=>$v) { $area .= ' L '.$getX($i).','.$getY($v); }
                $area .= ' L '.$getX($n-1).','.$svgH.' L '.$getX(0).','.$svgH.' Z';
            @endphp
            <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" style="width:100%;height:100px;display:block;">
                <defs>
                    <linearGradient id="ag" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#2D91CE" stop-opacity=".2"/>
                        <stop offset="100%" stop-color="#2D91CE" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <path d="{{ $area }}" fill="url(#ag)"/>
                <polyline points="{{ $linePts }}" fill="none" stroke="#2D91CE" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                @foreach($pts as $i => $v)
                @if($v > 0)
                <circle cx="{{ $getX($i) }}" cy="{{ $getY($v) }}" r="3" fill="#2D91CE"/>
                @endif
                @endforeach
            </svg>
            <div style="display:flex;justify-content:space-between;margin-top:6px;">
                <span style="font-size:10px;color:var(--ink-6);">{{ $last30->keys()->first() }}</span>
                <span style="font-size:10px;color:var(--ink-4);font-weight:600;">Total: {{ $last30->sum() }} audituri</span>
                <span style="font-size:10px;color:var(--ink-6);">{{ $last30->keys()->last() }}</span>
            </div>
        </div>
    </div>

    <!-- Distribuție scoruri -->
    <div class="card">
        <div class="card-head">
            <div>
                <div class="card-title">Distribuție scoruri</div>
                <div class="card-sub">{{ $totalDist }} audituri completate</div>
            </div>
        </div>
        @php $distData = [['Bun ≥80','#22c55e','bun'],['Mediu 50-79','#f59e0b','mediu'],['Slab <50','#ef4444','slab']]; @endphp
        @foreach($distData as [$lbl, $col, $key])
        @php $cnt = $scoreDist[$key] ?? 0; $pct = $totalDist > 0 ? round($cnt/$totalDist*100) : 0; @endphp
        <div class="dist-row">
            <div class="dist-label" style="color:{{ $col }}">{{ $lbl }}</div>
            <div class="dist-bar-wrap">
                <div class="dist-bar" style="width:{{ $pct }}%;background:{{ $col }};"></div>
            </div>
            <div class="dist-count">{{ $cnt }}</div>
        </div>
        @endforeach
        <div style="padding:12px 20px;border-top:1px solid var(--rule);display:flex;gap:16px;">
            @foreach($distData as [$lbl,$col,$key])
            @php $pct2 = $totalDist > 0 ? round(($scoreDist[$key]??0)/$totalDist*100) : 0; @endphp
            <div style="display:flex;align-items:center;gap:5px;">
                <div style="width:8px;height:8px;border-radius:2px;background:{{ $col }};flex-shrink:0;"></div>
                <span style="font-size:11px;color:var(--ink-5);">{{ $pct2 }}%</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Ultimele audituri -->
<div class="card">
    <div class="card-head">
        <div>
            <div class="card-title">Ultimele audituri</div>
            <div class="card-sub">Cele mai recente 5 audituri din platformă</div>
        </div>
        <a href="{{ route('admin.audits') }}" class="btn btn-dark">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Vezi toate
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr><th>Website</th><th>Email</th><th>Scor</th><th>Status</th><th>Data</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($recentAudits as $a)
                @php
                    $sc = $a->score_total;
                    $sC = $sc >= 80 ? 'g' : ($sc >= 50 ? 'a' : 'r');
                    $stM = ['completed'=>['g','Finalizat'],'pending'=>['a','În așteptare'],'processing'=>['a','În procesare'],'failed'=>['r','Eroare']];
                    $st  = $stM[$a->status] ?? ['a',ucfirst($a->status)];
                @endphp
                <tr>
                    <td><div class="tbl-url" title="{{ $a->url }}">{{ $a->url }}</div></td>
                    <td style="font-size:12px;color:var(--ink-4);">{{ $a->email }}</td>
                    <td>
                        @if($sc !== null)<div class="score-chip {{ $sC }}">{{ $sc }}</div>
                        @else<span style="color:var(--ink-6)">—</span>@endif
                    </td>
                    <td><span class="badge {{ $st[0] }}">{{ $st[1] }}</span></td>
                    <td style="font-size:11px;color:var(--ink-5);white-space:nowrap;">{{ $a->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($a->status === 'completed' && $a->public_token)
                        <a href="{{ route('audit.report', $a->public_token) }}" class="btn" target="_blank" style="font-size:11px;padding:5px 10px;">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Raport
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection