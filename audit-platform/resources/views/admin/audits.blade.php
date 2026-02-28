@extends('layouts.admin')
@section('title','Audituri')
@section('page_title','Audituri')
@section('breadcrumb','Audituri')

@push('styles')
<style>
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;}
.stat-mini{background:var(--paper);border:1px solid var(--rule);border-radius:10px;padding:14px 16px;}
.stat-mini-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-5);margin-bottom:4px;}
.stat-mini-val{font-size:22px;font-weight:800;letter-spacing:-1px;color:var(--ink);}
.stat-mini-val.blue{color:var(--blue);}
.stat-mini-val.green{color:var(--green);}
.stat-mini-val.amber{color:var(--amber);}
.stat-mini-val.red{color:var(--red);}

.filters{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center;}
.filter-input{flex:1;min-width:200px;height:36px;border:1px solid var(--rule);border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:var(--ink);background:var(--paper);outline:none;transition:border-color .15s;}
.filter-input:focus{border-color:var(--blue);}
.filter-select{height:36px;border:1px solid var(--rule);border-radius:8px;padding:0 10px;font-size:12px;font-family:inherit;color:var(--ink-3);background:var(--paper);outline:none;cursor:pointer;}
.btn-filter{height:36px;padding:0 16px;background:var(--ink);color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;}
.btn-filter svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.btn-reset{height:36px;padding:0 12px;background:transparent;color:var(--ink-4);border:1px solid var(--rule);border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;}
.btn-reset:hover{color:var(--ink);border-color:var(--ink-4);}

.card{background:var(--paper);border:1px solid var(--rule);border-radius:12px;overflow:hidden;}
.tbl{width:100%;border-collapse:collapse;font-size:12px;}
.tbl th{text-align:left;padding:10px 16px;font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-5);border-bottom:1px solid var(--rule);background:var(--paper-2);white-space:nowrap;}
.tbl td{padding:12px 16px;border-bottom:1px solid var(--rule);vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:var(--paper-2);}
.tbl-url{font-size:12px;font-weight:600;color:var(--ink-3);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.tbl-email{font-size:11px;color:var(--ink-5);margin-top:2px;}
.badge{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;}
.badge.g{background:var(--green-bg);color:var(--green);}
.badge.a{background:var(--amber-bg);color:var(--amber);}
.badge.r{background:var(--red-bg);color:var(--red);}
.chip{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;}
.chip.r{background:var(--red-bg);color:var(--red);}
.chip.a{background:var(--amber-bg);color:var(--amber);}
.chips{display:flex;gap:3px;}
.score-chip{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;font-size:11px;font-weight:800;}
.score-chip.g{background:var(--green-bg);color:var(--green);border:1px solid var(--green-bd);}
.score-chip.a{background:var(--amber-bg);color:var(--amber);border:1px solid var(--amber-bd);}
.score-chip.r{background:var(--red-bg);color:var(--red);border:1px solid var(--red-bd);}

.tbl-actions{display:flex;gap:4px;align-items:center;}
.btn-sm{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:4px 10px;border:1px solid var(--rule);border-radius:6px;background:var(--paper);color:var(--ink-3);text-decoration:none;cursor:pointer;transition:all .15s;font-family:inherit;}
.btn-sm:hover{border-color:var(--ink-4);color:var(--ink);}
.btn-sm.danger:hover{background:var(--red-bg);border-color:var(--red-bd);color:var(--red);}
.btn-sm svg{width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

/* Pagination */
.pagination{display:flex;align-items:center;gap:4px;padding:14px 20px;border-top:1px solid var(--rule);flex-wrap:wrap;}
.page-btn{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border:1px solid var(--rule);border-radius:6px;font-size:12px;font-weight:500;color:var(--ink-4);text-decoration:none;transition:all .15s;}
.page-btn:hover{border-color:var(--ink-4);color:var(--ink);}
.page-btn.active{background:var(--ink);color:white;border-color:var(--ink);}
.page-info{font-size:12px;color:var(--ink-5);margin-left:auto;}

/* ── RESPONSIVE ── */
@media(max-width:768px){
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .filters{flex-direction:column;}
    .filter-input{min-width:unset;width:100%;}
    /* Tabela: ascunde coloane secundare */
    .tbl th:nth-child(1),
    .tbl td:nth-child(1){ display:none; } /* # ID */
    .tbl th:nth-child(6),
    .tbl td:nth-child(6){ display:none; } /* User */
    .tbl th:nth-child(7),
    .tbl td:nth-child(7){ display:none; } /* Data */
    .tbl-url{max-width:130px;}
}
@media(max-width:480px){
    .tbl th:nth-child(4),
    .tbl td:nth-child(4){ display:none; } /* Probleme */
    .tbl-url{max-width:100px;}
    .tbl-actions{flex-direction:column;}
}
</style>
@endpush

@section('content')

<div class="stats-row">
    <div class="stat-mini"><div class="stat-mini-label">Total</div><div class="stat-mini-val blue">{{ $stats['total'] }}</div></div>
    <div class="stat-mini"><div class="stat-mini-label">Finalizate</div><div class="stat-mini-val green">{{ $stats['completed'] }}</div></div>
    <div class="stat-mini"><div class="stat-mini-label">În procesare</div><div class="stat-mini-val amber">{{ $stats['pending'] }}</div></div>
    <div class="stat-mini"><div class="stat-mini-label">Eșuate</div><div class="stat-mini-val red">{{ $stats['failed'] }}</div></div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.audits') }}">
    <div class="filters">
        <input type="text" name="search" class="filter-input" placeholder="Caută URL sau email..." value="{{ request('search') }}">
        <select name="status" class="filter-select">
            <option value="">Toate statusurile</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Finalizat</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>În așteptare</option>
            <option value="processing"{{ request('status') === 'processing'? 'selected' : '' }}>În procesare</option>
            <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Eșuat</option>
        </select>
        <select name="score" class="filter-select">
            <option value="">Toate scorurile</option>
            <option value="bun"   {{ request('score') === 'bun'   ? 'selected' : '' }}>Bun (≥80)</option>
            <option value="mediu" {{ request('score') === 'mediu' ? 'selected' : '' }}>Mediu (50-79)</option>
            <option value="slab"  {{ request('score') === 'slab'  ? 'selected' : '' }}>Slab (<50)</option>
        </select>
        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
            Caută
        </button>
        @if(request()->anyFilled(['search','status','score']))
        <a href="{{ route('admin.audits') }}" class="btn-reset">✕ Resetează</a>
        @endif
    </div>
</form>

<div class="card">
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th><th>Website</th><th>Scor</th><th>Probleme</th><th>Status</th><th>User</th><th>Data</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $a)
                @php
                    $sc = $a->score_total;
                    $sC = $sc >= 80 ? 'g' : ($sc >= 50 ? 'a' : 'r');
                    $cr = $a->issues->where('severity','critical')->count();
                    $wa = $a->issues->where('severity','warning')->count();
                    $stM = ['completed'=>['g','Finalizat'],'pending'=>['a','În așteptare'],'processing'=>['a','În procesare'],'failed'=>['r','Eroare']];
                    $st  = $stM[$a->status] ?? ['a',ucfirst($a->status)];
                @endphp
                <tr>
                    <td style="font-size:11px;color:var(--ink-6);font-family:monospace;">{{ $a->id }}</td>
                    <td>
                        <div class="tbl-url" title="{{ $a->url }}">{{ $a->url }}</div>
                        <div class="tbl-email">{{ $a->email }}</div>
                    </td>
                    <td>
                        @if($sc !== null)<div class="score-chip {{ $sC }}">{{ $sc }}</div>
                        @else<span style="color:var(--ink-6)">—</span>@endif
                    </td>
                    <td>
                        <div class="chips">
                            @if($cr > 0)<span class="chip r">{{ $cr }}🔴</span>@endif
                            @if($wa > 0)<span class="chip a">{{ $wa }}🟡</span>@endif
                            @if($cr === 0 && $wa === 0 && $a->status === 'completed')<span style="font-size:11px;color:var(--green)">✓ OK</span>@endif
                        </div>
                    </td>
                    <td><span class="badge {{ $st[0] }}">{{ $st[1] }}</span></td>
                    <td style="font-size:11px;color:var(--ink-4);">{{ $a->user?->name ?? '—' }}</td>
                    <td style="font-size:11px;color:var(--ink-5);white-space:nowrap;">
                        {{ $a->created_at->format('d.m.Y') }}<br>
                        <span style="font-size:10px;">{{ $a->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <div class="tbl-actions">
                            @if($a->status === 'completed' && $a->public_token)
                            <a href="{{ route('audit.report', $a->public_token) }}" target="_blank" class="btn-sm">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Raport
                            </a>
                            @endif
                            <form method="POST" action="{{ route('admin.audits.delete', $a) }}" onsubmit="return confirm('Ștergi auditul pentru {{ addslashes($a->url) }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm danger">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Șterge
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--ink-5);font-size:13px;">
                        Niciun audit găsit
                        @if(request()->anyFilled(['search','status','score']))
                        — <a href="{{ route('admin.audits') }}" style="color:var(--blue);">resetează filtrele</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($audits->hasPages())
    <div class="pagination">
        @if($audits->onFirstPage())
            <span class="page-btn" style="opacity:.4">‹</span>
        @else
            <a href="{{ $audits->previousPageUrl() }}" class="page-btn">‹</a>
        @endif

        @foreach($audits->getUrlRange(max(1, $audits->currentPage()-2), min($audits->lastPage(), $audits->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page === $audits->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($audits->hasMorePages())
            <a href="{{ $audits->nextPageUrl() }}" class="page-btn">›</a>
        @else
            <span class="page-btn" style="opacity:.4">›</span>
        @endif

        <span class="page-info">{{ $audits->firstItem() }}–{{ $audits->lastItem() }} din {{ $audits->total() }}</span>
    </div>
    @endif
</div>

@endsection