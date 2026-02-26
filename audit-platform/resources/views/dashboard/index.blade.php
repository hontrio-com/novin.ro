@extends('layouts.dashboard')
@section('title','Dashboard')
@section('page_title','Dashboard')

@section('content')

<div class="stats-grid">
    <div class="card stat">
        <div class="stat-label">Audituri totale</div>
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-sub">de la crearea contului</div>
    </div>
    <div class="card stat">
        <div class="stat-label">Scor mediu</div>
        <div class="stat-val">{{ number_format($stats['avg_score'], 0) }}<span class="stat-unit">/100</span></div>
        <div class="stat-sub">pe toate auditurile completate</div>
    </div>
    <div class="card stat">
        <div class="stat-label">Probleme critice</div>
        <div class="stat-val red">{{ $stats['critical'] }}</div>
        <div class="stat-sub">necesita atentie urgenta</div>
    </div>
    <div class="card stat">
        <div class="stat-label">Ultimul audit</div>
        <div class="stat-val" style="font-size:16px;letter-spacing:-.3px;margin-top:6px">{{ $stats['last_audit'] }}</div>
        <div class="stat-sub">&nbsp;</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Auditurile tale</div>
            <div class="card-desc">Toate auditurile efectuate pe contul tau</div>
        </div>
        <a href="{{ route('home') }}" class="btn btn-dark btn-sm">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Audit nou
        </a>
    </div>

    @if($audits->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
            </div>
            <div class="empty-title">Niciun audit efectuat inca</div>
            <div class="empty-desc">Porneste primul audit pentru a vedea rezultatele si recomandarile pentru site-ul tau.</div>
            <a href="{{ route('home') }}" class="btn btn-dark btn-lg">
                Porneste primul audit
                <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    @else
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Scor</th>
                        <th>Probleme</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $audit)
                    <tr>
                        <td>
                            <div class="tbl-url" title="{{ $audit->url }}">{{ $audit->url }}</div>
                            <div class="tbl-email">{{ $audit->email }}</div>
                        </td>
                        <td>
                            @if($audit->score_total !== null)
                                @php $sc = $audit->score_total >= 80 ? 'g' : ($audit->score_total >= 50 ? 'a' : 'r'); @endphp
                                <span class="score {{ $sc }}">{{ $audit->score_total }}</span>
                            @else
                                <span style="color:var(--ink-6)">—</span>
                            @endif
                        </td>
                        <td>
                            @if($audit->status === 'completed')
                                @php
                                    $crit = $audit->issues->where('severity','critical')->count();
                                    $warn = $audit->issues->where('severity','warning')->count();
                                @endphp
                                <div class="chips">
                                    @if($crit > 0)<span class="chip r">{{ $crit }} critice</span>@endif
                                    @if($warn > 0)<span class="chip a">{{ $warn }} avert.</span>@endif
                                    @if($crit === 0 && $warn === 0)<span class="chip g">OK</span>@endif
                                </div>
                            @else
                                <span style="color:var(--ink-6);font-size:12px">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $map = ['completed'=>['g','Finalizat'],'pending'=>['a','In asteptare'],'processing'=>['a','In procesare'],'failed'=>['r','Eroare']];
                                $st  = $map[$audit->status] ?? ['a', ucfirst($audit->status)];
                            @endphp
                            <span class="badge {{ $st[0] }}">{{ $st[1] }}</span>
                        </td>
                        <td style="font-size:12px;color:var(--ink-5);white-space:nowrap">
                            {{ $audit->created_at->format('d.m.Y') }}
                            <span style="display:block;font-size:11px">{{ $audit->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            @if($audit->status === 'completed' && $audit->public_token)
                                <a href="{{ route('audit.report', $audit->public_token) }}" class="tbl-action">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Vezi raport
                                </a>
                            @elseif(in_array($audit->status, ['processing','pending']))
                                <a href="{{ route('audit.progress', $audit->id) }}" class="tbl-action" style="color:var(--ink-4);border-color:var(--rule);background:var(--paper-2)">
                                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-6.59"/></svg>
                                    In curs
                                </a>
                            @else
                                <span style="color:var(--ink-6);font-size:12px">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection