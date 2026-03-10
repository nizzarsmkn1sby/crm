@extends('layouts.app')
@section('title', $lead->name.' — Lead Detail')
@section('breadcrumb')
    <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-white text-sm">Lead</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">{{ $lead->name }}</span>
@endsection
@section('content')
<div class="fade-in">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0" style="background:rgba(99,102,241,0.2);color:#a5b4fc;">
                {{ strtoupper(substr($lead->name,0,1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $lead->name }}</h1>
                @if($lead->company)<div class="text-gray-400 mt-1"><i class="bi bi-building mr-2"></i>{{ $lead->company }} @if($lead->position)— {{ $lead->position }}@endif</div>@endif
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span>
                    <span class="badge badge-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span>
                    @if($lead->pipelineStage)
                        <span class="badge" style="background:{{ $lead->pipelineStage->color }}20;color:{{ $lead->pipelineStage->color }};">{{ $lead->pipelineStage->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- WhatsApp --}}
            @if($lead->whatsapp || $lead->phone)
                <button class="btn btn-whatsapp" onclick="document.getElementById('waModal').classList.remove('hidden')">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </button>
            @endif
            {{-- Email --}}
            @if($lead->email)
                <button class="btn btn-secondary" onclick="document.getElementById('emailModal').classList.remove('hidden')">
                    <i class="bi bi-envelope"></i> Email
                </button>
            @endif
            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Hapus lead ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>

    <div class="dashboard-row grid-cols-2" style="grid-template-columns: 2fr 1fr; gap: 16px;">
        {{-- Left Column --}}
        <div class="flex flex-col gap-4">
            {{-- Contact Info --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-person-lines-fill text-indigo-400 mr-2"></i>Informasi Kontak</h3>
                <div class="grid-cols-2" style="gap:12px;">
                    @if($lead->email)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Email</div>
                            <a href="mailto:{{ $lead->email }}" class="text-sm text-blue-400 hover:text-blue-300">{{ $lead->email }}</a>
                        </div>
                    @endif
                    @if($lead->phone)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Telepon</div>
                            <a href="tel:{{ $lead->phone }}" class="text-sm text-gray-300">{{ $lead->phone }}</a>
                        </div>
                    @endif
                    @if($lead->whatsapp)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">WhatsApp</div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$lead->whatsapp) }}" target="_blank" class="text-sm text-green-400">{{ $lead->whatsapp }}</a>
                        </div>
                    @endif
                    @if($lead->source)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Sumber</div>
                            <span class="text-sm text-gray-300 capitalize">{{ $lead->source }}</span>
                        </div>
                    @endif
                    @if($lead->estimated_value)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Estimasi Nilai</div>
                            <span class="text-sm font-bold" style="color:#6ee7b7">Rp {{ number_format($lead->estimated_value,0,',','.') }}</span>
                        </div>
                    @endif
                    @if($lead->expected_close_date)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Target Closing</div>
                            <span class="text-sm text-gray-300">{{ $lead->expected_close_date->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
                @if($lead->notes)
                    <div class="mt-4 pt-4 border-t border-gray-800">
                        <div class="text-xs text-gray-500 mb-2">Catatan</div>
                        <p class="text-sm text-gray-300">{{ $lead->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Activity Log Form --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-plus-circle text-green-400 mr-2"></i>Tambah Aktivitas / Catatan</h3>
                <form action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="grid-cols-2" style="gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Tipe Aktivitas</label>
                            <select name="type" class="form-control">
                                <option value="note">📝 Catatan</option>
                                <option value="call">📞 Telepon</option>
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="email">✉️ Email</option>
                                <option value="meeting">🤝 Meeting</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subjek</label>
                            <input type="text" name="subject" class="form-control" placeholder="Contoh: Follow up via telepon" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi / Hasil</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tulis detail aktivitas..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Outcome</label>
                        <input type="text" name="outcome" class="form-control" placeholder="Contoh: Tertarik, jadwal demo besok">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Simpan Aktivitas</button>
                </form>
            </div>

            {{-- Activity Timeline --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-clock-history text-purple-400 mr-2"></i>Riwayat Aktivitas ({{ $lead->activities->count() }})</h3>
                @if($lead->activities->count() > 0)
                    <div class="timeline" style="padding-left:8px;">
                        @foreach($lead->activities->sortByDesc('created_at') as $act)
                            <div class="timeline-item">
                                <div class="timeline-icon" style="width:32px;height:32px;">
                                    <i class="bi {{ $act->type_icon }}" style="font-size:13px;"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-sm text-white">{{ $act->subject }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500">{{ $act->created_at->diffForHumans() }}</span>
                                            <form action="{{ route('activities.destroy', $act) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button class="text-xs text-gray-600 hover:text-red-400"><i class="bi bi-x"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    @if($act->description)<p class="text-xs text-gray-400 mt-1">{{ $act->description }}</p>@endif
                                    @if($act->outcome)<p class="text-xs text-green-400 mt-1">✓ {{ $act->outcome }}</p>@endif
                                    <div class="text-xs text-gray-600 mt-1">oleh {{ $act->user->name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding:30px;">
                        <i class="bi bi-activity block text-3xl mb-2 opacity-30"></i>
                        <p class="text-sm">Belum ada aktivitas tercatat</p>
                    </div>
                @endif
            </div>

            {{-- Documents --}}
            <div class="crm-card">
                <div class="crm-card-header">
                    <h3 class="crm-card-title"><i class="bi bi-folder-fill text-yellow-400 mr-2"></i>Dokumen ({{ $lead->documents->count() }})</h3>
                </div>
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="flex gap-2">
                        <input type="file" name="file" class="form-control flex-1" required>
                        <input type="text" name="name" class="form-control" placeholder="Nama dokumen" style="width:180px;">
                        <button type="submit" class="btn btn-secondary btn-sm flex-shrink-0"><i class="bi bi-upload"></i> Upload</button>
                    </div>
                </form>
                @forelse($lead->documents as $doc)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-800/50 last:border-0">
                        <i class="bi {{ $doc->file_icon }} text-xl flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white truncate">{{ $doc->name }}</div>
                            <div class="text-xs text-gray-500">{{ $doc->file_size_formatted }} · {{ $doc->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-secondary btn-icon"><i class="bi bi-download"></i></a>
                            <form action="{{ route('documents.destroy', $doc) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada dokumen</p>
                @endforelse
            </div>
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-4">
            {{-- Quick Info --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-info-circle text-blue-400 mr-2"></i>Info Lead</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status</span>
                        <span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Prioritas</span>
                        <span class="badge badge-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Salesperson</span>
                        <span class="text-gray-300">{{ $lead->assignedUser?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Terakhir Dihubungi</span>
                        <span class="text-gray-300 text-xs">{{ $lead->last_contacted_at?->diffForHumans() ?? 'Belum' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Masuk Sejak</span>
                        <span class="text-gray-300 text-xs">{{ $lead->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Change Stage --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-kanban text-purple-400 mr-2"></i>Ubah Stage Pipeline</h3>
                <div class="flex flex-col gap-2">
                    @foreach($stages as $stage)
                        <button onclick="changeStage({{ $lead->id }}, {{ $stage->id }})"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all hover:bg-white/5 text-left {{ $lead->pipeline_stage_id == $stage->id ? 'bg-white/10 font-semibold' : 'text-gray-400' }}">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $stage->color }}"></span>
                            {{ $stage->name }}
                            @if($lead->pipeline_stage_id == $stage->id)<i class="bi bi-check ml-auto text-green-400"></i>@endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tasks --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-3"><i class="bi bi-check2-square text-orange-400 mr-2"></i>Task Terkait</h3>
                @forelse($lead->tasks->take(5) as $task)
                    <div class="flex items-center gap-2 py-2 border-b border-gray-800/50 last:border-0">
                        <form action="{{ route('tasks.complete', $task) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="w-5 h-5 rounded border {{ $task->status==='completed' ? 'bg-green-500 border-green-500' : 'border-gray-600 hover:border-green-400' }} flex items-center justify-center flex-shrink-0 transition-all">
                                @if($task->status==='completed')<i class="bi bi-check-lg text-xs text-white"></i>@endif
                            </button>
                        </form>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm {{ $task->status==='completed' ? 'line-through text-gray-500' : 'text-gray-300' }} truncate block">{{ $task->title }}</span>
                            @if($task->due_date)<span class="text-xs {{ $task->isOverdue() ? 'text-red-400' : 'text-gray-600' }}">{{ $task->due_date->format('d M Y') }}</span>@endif
                        </div>
                        <span class="badge badge-{{ $task->priority }}" style="font-size:10px;">{{ $task->priority }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-2">Belum ada task</p>
                @endforelse

                {{-- Quick add task --}}
                <form action="{{ route('tasks.store') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="priority" value="medium">
                    <div class="flex gap-2">
                        <input type="text" name="title" class="form-control text-sm" placeholder="+ Tambah task..." style="padding:7px 10px;" required>
                        <input type="datetime-local" name="due_date" class="form-control text-sm" style="padding:7px 10px;width:155px;">
                        <button type="submit" class="btn btn-primary btn-sm flex-shrink-0"><i class="bi bi-plus"></i></button>
                    </div>
                </form>
            </div>

            {{-- Deals --}}
            @if($lead->deals->count() > 0)
                <div class="crm-card">
                    <h3 class="crm-card-title mb-3"><i class="bi bi-bag-check text-green-400 mr-2"></i>Deal Terkait</h3>
                    @foreach($lead->deals as $deal)
                        <a href="{{ route('deals.show', $deal) }}" class="flex items-center gap-2 py-2 hover:bg-white/5 rounded-lg px-2 -mx-2">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-white">{{ $deal->title }}</div>
                                <div class="text-xs text-gray-500">{{ $deal->pipelineStage?->name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-emerald-400">Rp{{ number_format($deal->value/1000000,1) }}M</div>
                                <span class="badge badge-{{ $deal->status }}" style="font-size:10px;">{{ $deal->status }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- WhatsApp Modal --}}
<div id="waModal" class="hidden" x-data>
    <div class="modal-overlay" @click.self="document.getElementById('waModal').classList.add('hidden')">
        <div class="modal-box">
            <div class="modal-header">
                <h2 class="modal-title"><i class="bi bi-whatsapp text-green-400 mr-2"></i>Kirim Pesan WhatsApp</h2>
                <button class="modal-close" onclick="document.getElementById('waModal').classList.add('hidden')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('leads.send-whatsapp', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Kirim ke: <strong class="text-green-400">{{ $lead->whatsapp ?? $lead->phone }}</strong></label>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Pesan</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Halo {{ $lead->name }}, ..." required>Halo {{ $lead->name }}, perkenalkan saya dari WebCare. Saya ingin menghubungi Anda mengenai...</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('waModal').classList.add('hidden')">Batal</button>
                    <button type="submit" class="btn btn-whatsapp"><i class="bi bi-whatsapp"></i> Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Email Modal --}}
<div id="emailModal" class="hidden">
    <div class="modal-overlay" onclick="if(event.target===this)document.getElementById('emailModal').classList.add('hidden')">
        <div class="modal-box">
            <div class="modal-header">
                <h2 class="modal-title"><i class="bi bi-envelope text-blue-400 mr-2"></i>Kirim Email</h2>
                <button class="modal-close" onclick="document.getElementById('emailModal').classList.add('hidden')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('leads.send-email', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Ke: <strong class="text-blue-400">{{ $lead->email }}</strong></label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subjek</label>
                        <input type="text" name="subject" class="form-control" placeholder="Subjek email..." required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Pesan (HTML didukung)</label>
                        <textarea name="body" class="form-control" rows="6" placeholder="Isi email..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('emailModal').classList.add('hidden')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function changeStage(leadId, stageId) {
    fetch(`/leads/${leadId}/stage`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ pipeline_stage_id: stageId })
    }).then(r => r.json()).then(d => { if(d.success) location.reload() });
}
</script>
@endpush
@endsection
