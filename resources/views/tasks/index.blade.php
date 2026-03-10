@extends('layouts.app')
@section('title', 'Task Management')
@section('breadcrumb')
    <i class="bi bi-check2-square text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Task</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">Task Management</h1>
            <p class="page-subtitle">{{ $tasks->total() }} task ditemukan</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addTaskModal').classList.remove('hidden')">
            <i class="bi bi-plus-lg"></i> Tambah Task
        </button>
    </div>

    {{-- Quick Filters --}}
    <div class="flex gap-2 mb-4 flex-wrap">
        @foreach([
            'all' => ['label'=>'Semua','icon'=>'bi-list-ul'],
            'today' => ['label'=>'Hari Ini','icon'=>'bi-calendar-day'],
            'overdue' => ['label'=>'Terlambat','icon'=>'bi-exclamation-triangle'],
            'upcoming' => ['label'=>'7 Hari','icon'=>'bi-calendar-week'],
        ] as $key => $opt)
            <a href="{{ route('tasks.index', ['filter'=>$key]+request()->except('filter')) }}"
                class="btn btn-sm {{ request('filter',$key==='all'?'all':'') === $key ? 'btn-primary' : 'btn-secondary' }}">
                <i class="bi {{ $opt['icon'] }}"></i> {{ $opt['label'] }}
                @if($key==='overdue')
                    @php $cnt = \App\Models\Task::where('status','pending')->where('due_date','<',now())->count() @endphp
                    @if($cnt > 0)<span class="nav-badge" style="margin-left:4px;">{{ $cnt }}</span>@endif
                @endif
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('tasks.index') }}">
        <input type="hidden" name="filter" value="{{ request('filter','all') }}">
        <div class="filter-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari task..." class="filter-input" style="flex:1;min-width:180px;">
            <select name="status" class="filter-input">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="priority" class="filter-input">
                <option value="">Semua Prioritas</option>
                @foreach(['urgent'=>'Urgent','high'=>'High','medium'=>'Medium','low'=>'Low'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('priority')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            @if(auth()->user()->isManager())
                <select name="assigned_to" class="filter-input">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned_to')==$user->id?'selected':'' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>

    {{-- Tasks Table --}}
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>TASK</th>
                    <th>PRIORITAS</th>
                    <th>STATUS</th>
                    <th>JATUH TEMPO</th>
                    <th>TERKAIT</th>
                    <th>DITUGASKAN KE</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr class="{{ $task->status==='completed' ? 'opacity-60' : '' }}">
                        <td>
                            <form action="{{ route('tasks.complete', $task) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-6 h-6 rounded-md border flex items-center justify-center transition-all {{ $task->status==='completed' ? 'bg-green-500 border-green-500' : 'border-gray-600 hover:border-green-400' }}">
                                    @if($task->status==='completed')<i class="bi bi-check text-white text-xs"></i>@endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="font-semibold text-sm {{ $task->status==='completed' ? 'line-through text-gray-500' : 'text-white' }}">{{ $task->title }}</div>
                            @if($task->description)<div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($task->description, 60) }}</div>@endif
                        </td>
                        <td><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $task->status }}">
                                {{ ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$task->status] ?? $task->status }}
                            </span>
                        </td>
                        <td>
                            @if($task->due_date)
                                <span class="{{ $task->isOverdue() ? 'text-red-400' : 'text-gray-400' }} text-sm flex items-center gap-1">
                                    @if($task->isOverdue())<i class="bi bi-exclamation-circle"></i>@endif
                                    {{ $task->due_date->format('d M Y, H:i') }}
                                </span>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-xs text-gray-400">
                                @if($task->lead)<a href="{{ route('leads.show', $task->lead) }}" class="hover:text-white"><i class="bi bi-person mr-1"></i>{{ $task->lead->name }}</a>@endif
                                @if($task->deal)<a href="{{ route('deals.show', $task->deal) }}" class="hover:text-white"><i class="bi bi-bag-check mr-1"></i>{{ $task->deal->title }}</a>@endif
                                @if(!$task->lead && !$task->deal)<span class="text-gray-600">—</span>@endif
                            </div>
                        </td>
                        <td>
                            @if($task->assignedUser)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $task->assignedUser->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-xs text-gray-400">{{ $task->assignedUser->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus task ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-icon btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-check2-circle text-green-500"></i></div>
                                <div class="empty-state-title">Tidak ada task ditemukan</div>
                                <div class="empty-state-desc">
                                    @if(request('filter')==='overdue')
                                        Tidak ada task yang terlambat. Kerja bagus! 🎉
                                    @else
                                        Belum ada task. Tambah task pertama Anda!
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $tasks->links() }}</div>
</div>

{{-- Add Task Modal --}}
<div id="addTaskModal" class="hidden">
    <div class="modal-overlay" onclick="if(event.target===this)document.getElementById('addTaskModal').classList.add('hidden')">
        <div class="modal-box">
            <div class="modal-header">
                <h2 class="modal-title"><i class="bi bi-check2-square text-indigo-400 mr-2"></i>Tambah Task Baru</h2>
                <button class="modal-close" onclick="document.getElementById('addTaskModal').classList.add('hidden')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Judul Task *</label>
                        <input type="text" name="title" class="form-control" placeholder="Apa yang perlu dilakukan?" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detail task..."></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Prioritas *</label>
                            <select name="priority" class="form-control" required>
                                <option value="low">🟢 Low</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="urgent">🔴 Urgent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ditugaskan ke</label>
                            <select name="assigned_to" class="form-control">
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id===auth()->id()?'selected':'' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="datetime-local" name="due_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reminder</label>
                            <input type="datetime-local" name="reminder_at" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addTaskModal').classList.add('hidden')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
