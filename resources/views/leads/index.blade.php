@extends('layouts.app')
@section('title', 'Manajemen Lead')
@section('breadcrumb')
    <i class="bi bi-person-plus text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Lead</span>
@endsection

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Lead</h1>
            <p class="page-subtitle">{{ $leads->total() }} lead ditemukan</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Export Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="btn btn-secondary flex items-center gap-1">
                    <i class="bi bi-download"></i>
                    <span class="hidden md:inline">Export</span>
                    <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
                <div class="user-dropdown" x-show="open" @click.outside="open = false" x-transition style="width:180px;">
                    <a href="{{ route('export.leads.csv', request()->all()) }}" class="dropdown-item">
                        <i class="bi bi-filetype-csv text-green-400"></i> Export CSV
                    </a>
                    <a href="{{ route('export.leads.pdf', request()->all()) }}" class="dropdown-item">
                        <i class="bi bi-filetype-pdf text-red-400"></i> Export PDF
                    </a>
                </div>
            </div>
            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Lead
            </a>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-bottom:16px;">
        @foreach(['new'=>'Baru','contacted'=>'Dihubungi','qualified'=>'Qualified','proposal'=>'Proposal','negotiation'=>'Negosiasi','won'=>'Won','lost'=>'Lost'] as $st => $label)
            <a href="{{ route('leads.index', ['status'=>$st]) }}"
                class="crm-card text-center py-3 hover:border-indigo-500/40 transition-all {{ request('status')===$st ? 'border-indigo-500/60' : '' }}"
                style="{{ request('status')===$st ? 'background:rgba(99,102,241,0.08)' : '' }}">
                <div class="text-xl font-bold text-white">{{ $statusCounts[$st] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $label }}</div>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('leads.index') }}" id="filter-form">
        <div class="filter-bar">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="🔍 Cari nama, email, telepon, perusahaan..."
                    class="filter-input w-full">
            </div>
            <select name="status" class="filter-input">
                <option value="">Semua Status</option>
                @foreach(['new'=>'Baru','contacted'=>'Dihubungi','qualified'=>'Qualified','proposal'=>'Proposal','negotiation'=>'Negosiasi','won'=>'Won','lost'=>'Lost'] as $val => $lab)
                    <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lab }}</option>
                @endforeach
            </select>
            <select name="priority" class="filter-input">
                <option value="">Semua Prioritas</option>
                <option value="urgent" {{ request('priority')==='urgent'?'selected':'' }}>🔴 Urgent</option>
                <option value="high"   {{ request('priority')==='high'?'selected':'' }}>🟠 High</option>
                <option value="medium" {{ request('priority')==='medium'?'selected':'' }}>🟡 Medium</option>
                <option value="low"    {{ request('priority')==='low'?'selected':'' }}>🟢 Low</option>
            </select>
            <select name="source" class="filter-input">
                <option value="">Semua Sumber</option>
                @foreach(['website','referral','campaign','whatsapp','email','manual'] as $src)
                    <option value="{{ $src }}" {{ request('source')===$src?'selected':'' }}>{{ ucfirst($src) }}</option>
                @endforeach
            </select>
            <select name="assigned_to" class="filter-input">
                <option value="">Semua Salesperson</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_to')==$user->id?'selected':'' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
    </form>

    {{-- Bulk Actions Bar (muncul kalau ada yang dicentang) --}}
    <div id="bulk-bar" class="hidden mb-3 p-3 rounded-xl flex items-center gap-3"
        style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.3);">
        <span class="text-sm text-indigo-300 font-semibold">
            <span id="bulk-count">0</span> lead dipilih
        </span>
        <div class="flex items-center gap-2 ml-auto">
            {{-- Bulk: Ubah Status --}}
            <form id="bulk-status-form" method="POST" action="{{ route('leads.bulk') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="action" value="status">
                <div id="bulk-ids-status"></div>
                <select name="status" class="filter-input" style="height:32px;font-size:12px;">
                    <option value="">Ubah Status ke...</option>
                    @foreach(['new','contacted','qualified','proposal','negotiation','won','lost'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-tags"></i> Terapkan</button>
            </form>
            {{-- Bulk: Assign --}}
            <form id="bulk-assign-form" method="POST" action="{{ route('leads.bulk') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="action" value="assign">
                <div id="bulk-ids-assign"></div>
                <select name="assign_to" class="filter-input" style="height:32px;font-size:12px;">
                    <option value="">Assign ke...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-person-check"></i> Assign</button>
            </form>
            {{-- Bulk: Export CSV --}}
            <form id="bulk-export-form" method="POST" action="{{ route('leads.bulk') }}">
                @csrf
                <input type="hidden" name="action" value="export">
                <div id="bulk-ids-export"></div>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export</button>
            </form>
            {{-- Bulk: Delete --}}
            <form id="bulk-delete-form" method="POST" action="{{ route('leads.bulk') }}"
                onsubmit="return confirm('Yakin hapus ' + document.getElementById('bulk-count').textContent + ' lead?')">
                @csrf
                <input type="hidden" name="action" value="delete">
                <div id="bulk-ids-delete"></div>
                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Hapus</button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="crm-table-wrapper">
        <table class="crm-table" id="leads-table">
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="select-all" class="rounded" style="width:16px;height:16px;cursor:pointer;">
                    </th>
                    <th>
                        <a href="{{ route('leads.index', array_merge(request()->all(), ['sort'=>'name','dir'=>request('sort')==='name'&&request('dir')==='asc'?'desc':'asc'])) }}"
                            class="flex items-center gap-1 hover:text-white">
                            NAMA / PERUSAHAAN
                            @if(request('sort')==='name') <i class="bi bi-arrow-{{ request('dir')==='asc'?'up':'down' }}-short"></i> @endif
                        </a>
                    </th>
                    <th>KONTAK</th>
                    <th>SUMBER</th>
                    <th>STAGE</th>
                    <th>PRIORITAS</th>
                    <th>STATUS</th>
                    <th>
                        <a href="{{ route('leads.index', array_merge(request()->all(), ['sort'=>'estimated_value','dir'=>request('sort')==='estimated_value'&&request('dir')==='asc'?'desc':'asc'])) }}"
                            class="flex items-center gap-1 hover:text-white">
                            NILAI
                            @if(request('sort')==='estimated_value') <i class="bi bi-arrow-{{ request('dir')==='asc'?'up':'down' }}-short"></i> @endif
                        </a>
                    </th>
                    <th>SALESPERSON</th>
                    <th>TERAKHIR DIHUBUNGI</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                    <tr class="lead-row" data-id="{{ $lead->id }}">
                        <td>
                            <input type="checkbox" class="lead-checkbox rounded" value="{{ $lead->id }}"
                                style="width:16px;height:16px;cursor:pointer;">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                                    style="background:rgba(99,102,241,0.15);color:#a5b4fc;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('leads.show', $lead) }}" class="font-semibold text-white hover:text-indigo-400 transition-colors">{{ $lead->name }}</a>
                                    @if($lead->company)
                                        <div class="text-xs text-gray-500">{{ $lead->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1">
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="text-xs text-gray-400 hover:text-white flex items-center gap-1">
                                        <i class="bi bi-telephone text-green-400"></i> {{ $lead->phone }}
                                    </a>
                                @endif
                                @if($lead->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$lead->whatsapp) }}" target="_blank" class="text-xs text-gray-400 hover:text-green-400 flex items-center gap-1">
                                        <i class="bi bi-whatsapp text-green-500"></i> WA
                                    </a>
                                @endif
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}" class="text-xs text-gray-400 hover:text-blue-400 flex items-center gap-1">
                                        <i class="bi bi-envelope text-blue-400"></i> Email
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td><span class="text-sm capitalize text-gray-400">{{ $lead->source }}</span></td>
                        <td>
                            @if($lead->pipelineStage)
                                <span class="badge" style="background:{{ $lead->pipelineStage->color }}20;color:{{ $lead->pipelineStage->color }};border:1px solid {{ $lead->pipelineStage->color }}40;">
                                    {{ $lead->pipelineStage->name }}
                                </span>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span></td>
                        <td><span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span></td>
                        <td>
                            @if($lead->estimated_value)
                                <span class="font-semibold" style="color:#6ee7b7">Rp{{ number_format($lead->estimated_value/1000000,1) }}M</span>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->assignedUser)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $lead->assignedUser->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-xs text-gray-400">{{ $lead->assignedUser->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-600 text-xs">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->last_contacted_at)
                                <span class="text-xs text-gray-400">{{ $lead->last_contacted_at->diffForHumans() }}</span>
                            @else
                                <span class="text-xs text-red-400">Belum dihubungi</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-secondary btn-icon" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-secondary btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Yakin hapus lead {{ addslashes($lead->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-icon" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-person-x"></i></div>
                                <div class="empty-state-title">Tidak ada lead ditemukan</div>
                                <div class="empty-state-desc">Coba ubah filter pencarian atau <a href="{{ route('leads.create') }}" class="text-indigo-400">tambah lead baru</a>.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $leads->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
// ─── Bulk Select Logic ────────────────────────────────────────────────────────
const selectAll   = document.getElementById('select-all');
const bulkBar     = document.getElementById('bulk-bar');
const bulkCount   = document.getElementById('bulk-count');
const checkboxes  = () => document.querySelectorAll('.lead-checkbox');

function getSelectedIds() {
    return [...checkboxes()].filter(c => c.checked).map(c => c.value);
}

function syncBulkIds() {
    const ids = getSelectedIds();
    bulkCount.textContent = ids.length;
    bulkBar.classList.toggle('hidden', ids.length === 0);

    ['status','assign','export','delete'].forEach(form => {
        const container = document.getElementById(`bulk-ids-${form}`);
        container.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
    });
}

selectAll.addEventListener('change', e => {
    checkboxes().forEach(c => c.checked = e.target.checked);
    syncBulkIds();
});

document.addEventListener('change', e => {
    if (e.target.classList.contains('lead-checkbox')) {
        const total = checkboxes().length;
        const checked = [...checkboxes()].filter(c => c.checked).length;
        selectAll.checked = checked === total;
        selectAll.indeterminate = checked > 0 && checked < total;
        syncBulkIds();
    }
});

// Highlight row saat checkbox dicentang
document.addEventListener('change', e => {
    if (e.target.classList.contains('lead-checkbox')) {
        e.target.closest('tr').style.background = e.target.checked ? 'rgba(99,102,241,0.06)' : '';
    }
});
</script>
@endpush
