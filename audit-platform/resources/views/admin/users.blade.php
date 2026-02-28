@extends('layouts.admin')
@section('title','Utilizatori')
@section('page_title','Utilizatori')
@section('breadcrumb','Utilizatori')

@push('styles')
<style>
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;}
.stat-mini{background:var(--paper);border:1px solid var(--rule);border-radius:10px;padding:14px 16px;}
.stat-mini-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-5);margin-bottom:4px;}
.stat-mini-val{font-size:22px;font-weight:800;letter-spacing:-1px;color:var(--ink);}
.stat-mini-val.blue{color:var(--blue);}

.filters{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center;}
.filter-input{flex:1;min-width:200px;height:36px;border:1px solid var(--rule);border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:var(--ink);background:var(--paper);outline:none;transition:border-color .15s;}
.filter-input:focus{border-color:var(--blue);}
.filter-select{height:36px;border:1px solid var(--rule);border-radius:8px;padding:0 10px;font-size:12px;font-family:inherit;color:var(--ink-3);background:var(--paper);outline:none;cursor:pointer;}
.btn-filter{height:36px;padding:0 16px;background:var(--ink);color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;}
.btn-filter svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;}
.btn-reset{height:36px;padding:0 12px;background:transparent;color:var(--ink-4);border:1px solid var(--rule);border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;}
.btn-reset:hover{color:var(--ink);border-color:var(--ink-4);}

.card{background:var(--paper);border:1px solid var(--rule);border-radius:12px;overflow:hidden;}
.tbl{width:100%;border-collapse:collapse;font-size:12px;}
.tbl th{text-align:left;padding:10px 16px;font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-5);border-bottom:1px solid var(--rule);background:var(--paper-2);white-space:nowrap;}
.tbl td{padding:12px 16px;border-bottom:1px solid var(--rule);vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:var(--paper-2);}

.user-av{width:34px;height:34px;border-radius:50%;background:var(--blue-bg);border:1px solid var(--blue-bd);display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--blue);flex-shrink:0;}
.user-av.admin-av{background:#fef9c3;border-color:#fde68a;color:#a16207;}

.badge{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;}
.badge.admin{background:#fef9c3;color:#a16207;border:1px solid #fde68a;}
.badge.user{background:var(--paper-3);color:var(--ink-4);border:1px solid var(--rule);}
.badge.g{background:var(--green-bg);color:var(--green);}

.tbl-actions{display:flex;gap:4px;align-items:center;}
.btn-sm{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:4px 10px;border:1px solid var(--rule);border-radius:6px;background:var(--paper);color:var(--ink-3);text-decoration:none;cursor:pointer;transition:all .15s;font-family:inherit;}
.btn-sm:hover{border-color:var(--ink-4);color:var(--ink);}
.btn-sm.promote:hover{background:var(--amber-bg);border-color:var(--amber-bd);color:var(--amber);}
.btn-sm.demote:hover{background:var(--paper-3);border-color:var(--rule-2);color:var(--ink-4);}
.btn-sm.danger:hover{background:var(--red-bg);border-color:var(--red-bd);color:var(--red);}
.btn-sm svg{width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.btn-sm.self{opacity:.4;cursor:not-allowed;}

.pagination{display:flex;align-items:center;gap:4px;padding:14px 20px;border-top:1px solid var(--rule);flex-wrap:wrap;}
.page-btn{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border:1px solid var(--rule);border-radius:6px;font-size:12px;font-weight:500;color:var(--ink-4);text-decoration:none;transition:all .15s;}
.page-btn:hover{border-color:var(--ink-4);color:var(--ink);}
.page-btn.active{background:var(--ink);color:white;border-color:var(--ink);}
.page-info{font-size:12px;color:var(--ink-5);margin-left:auto;}

/* ── RESPONSIVE ── */
@media(max-width:768px){
    .stats-row{grid-template-columns:1fr 1fr;}
    .filters{flex-direction:column;}
    .filter-input{min-width:unset;width:100%;}
    /* Ascunde coloane secundare */
    .tbl th:nth-child(4),
    .tbl td:nth-child(4){ display:none; } /* Audituri */
    .tbl th:nth-child(5),
    .tbl td:nth-child(5){ display:none; } /* Înregistrat */
}
@media(max-width:480px){
    .tbl th:nth-child(2),
    .tbl td:nth-child(2){ display:none; } /* Email */
    .tbl-actions{flex-direction:column;}
    .btn-sm span{ display:none; }
}
</style>
@endpush

@section('content')

<div class="stats-row">
    <div class="stat-mini"><div class="stat-mini-label">Total utilizatori</div><div class="stat-mini-val blue">{{ $stats['total'] }}</div></div>
    <div class="stat-mini"><div class="stat-mini-label">Administratori</div><div class="stat-mini-val" style="color:var(--amber)">{{ $stats['admins'] }}</div></div>
    <div class="stat-mini"><div class="stat-mini-label">Noi (7 zile)</div><div class="stat-mini-val green">{{ $stats['new'] }}</div></div>
</div>

<form method="GET" action="{{ route('admin.users') }}">
    <div class="filters">
        <input type="text" name="search" class="filter-input" placeholder="Caută nume sau email..." value="{{ request('search') }}">
        <select name="role" class="filter-select">
            <option value="">Toate rolurile</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administratori</option>
            <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>Utilizatori</option>
        </select>
        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
            Caută
        </button>
        @if(request()->anyFilled(['search','role']))
        <a href="{{ route('admin.users') }}" class="btn-reset">✕ Resetează</a>
        @endif
    </div>
</form>

<div class="card">
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr><th>Utilizator</th><th>Email</th><th>Rol</th><th>Audituri</th><th>Înregistrat</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                @php $isSelf = $u->id === auth()->id(); @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="user-av {{ $u->is_admin ? 'admin-av' : '' }}">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <div>
                                <div style="font-size:12px;font-weight:600;color:var(--ink);">
                                    {{ $u->name }}
                                    @if($isSelf)<span style="font-size:9px;background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-bd);padding:1px 5px;border-radius:3px;margin-left:4px;">TU</span>@endif
                                </div>
                                <div style="font-size:11px;color:var(--ink-5);">ID: {{ $u->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--ink-4);">{{ $u->email }}</td>
                    <td>
                        <span class="badge {{ $u->is_admin ? 'admin' : 'user' }}">
                            {{ $u->is_admin ? '⭐ Admin' : 'User' }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:13px;font-weight:700;color:var(--ink);">{{ $u->audits_count }}</span>
                        <span style="font-size:11px;color:var(--ink-5);"> audituri</span>
                    </td>
                    <td style="font-size:11px;color:var(--ink-5);white-space:nowrap;">
                        {{ $u->created_at->format('d.m.Y') }}<br>
                        <span style="font-size:10px;">{{ $u->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div class="tbl-actions">
                            @if(!$isSelf)
                            {{-- Toggle admin --}}
                            <form method="POST" action="{{ route('admin.users.toggle-admin', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm {{ $u->is_admin ? 'demote' : 'promote' }}"
                                        onclick="return confirm('{{ $u->is_admin ? 'Retrogradezi' : 'Promovezi' }} utilizatorul {{ addslashes($u->name) }}?')">
                                    @if($u->is_admin)
                                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                        Retrogradează
                                    @else
                                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                        Promovează admin
                                    @endif
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.users.delete', $u) }}"
                                  onsubmit="return confirm('Ștergi utilizatorul {{ addslashes($u->name) }} și toate auditurile sale?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm danger">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                    Șterge
                                </button>
                            </form>
                            @else
                            <span class="btn-sm self">Contul tău</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--ink-5);font-size:13px;">
                        Niciun utilizator găsit
                        @if(request()->anyFilled(['search','role']))
                        — <a href="{{ route('admin.users') }}" style="color:var(--blue);">resetează filtrele</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination">
        @if($users->onFirstPage())
            <span class="page-btn" style="opacity:.4">‹</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="page-btn">‹</a>
        @endif
        @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page === $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="page-btn">›</a>
        @else
            <span class="page-btn" style="opacity:.4">›</span>
        @endif
        <span class="page-info">{{ $users->firstItem() }}–{{ $users->lastItem() }} din {{ $users->total() }}</span>
    </div>
    @endif
</div>

@endsection