@extends('layouts.app')
@section('title', 'Kalender Meeting')
@section('breadcrumb')
    <i class="bi bi-calendar3 text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Kalender Meeting</span>
@endsection
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
@endpush
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">Kalender Meeting</h1>
            <p class="page-subtitle">Jadwal dan manajemen meeting tim</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addMeetingModal').classList.remove('hidden')">
            <i class="bi bi-plus-lg"></i> Jadwalkan Meeting
        </button>
    </div>

    {{-- Calendar Container --}}
    <div class="crm-card" style="padding:24px;">
        <div id="calendar"></div>
    </div>
</div>

{{-- Add Meeting Modal --}}
<div id="addMeetingModal" class="hidden">
    <div class="modal-overlay" onclick="if(event.target===this)document.getElementById('addMeetingModal').classList.add('hidden')">
        <div class="modal-box" style="max-width:600px;">
            <div class="modal-header">
                <h2 class="modal-title"><i class="bi bi-calendar-plus text-indigo-400 mr-2"></i>Jadwalkan Meeting Baru</h2>
                <button class="modal-close" onclick="document.getElementById('addMeetingModal').classList.add('hidden')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form id="meetingForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Judul Meeting *</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Demo Produk - Ahmad Fauzi" required>
                    </div>
                    <div class="grid-cols-2" style="gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Mulai *</label>
                            <input type="datetime-local" name="start_at" id="startAt" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Selesai *</label>
                            <input type="datetime-local" name="end_at" id="endAt" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-cols-2" style="gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Lead Terkait</label>
                            <select name="lead_id" class="form-control">
                                <option value="">-- Pilih Lead --</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kontak Terkait</label>
                            <select name="contact_id" class="form-control">
                                <option value="">-- Pilih Kontak --</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Kantor Klien, Zoom, dll.">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Link Meeting (Zoom/Meet)</label>
                        <input type="url" name="meeting_link" class="form-control" placeholder="https://zoom.us/j/...">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Agenda meeting..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addMeetingModal').classList.add('hidden')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check"></i> Simpan Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Meeting Detail Modal --}}
<div id="meetingDetailModal" class="hidden">
    <div class="modal-overlay" onclick="if(event.target===this)document.getElementById('meetingDetailModal').classList.add('hidden')">
        <div class="modal-box">
            <div class="modal-header">
                <h2 class="modal-title" id="detailTitle"></h2>
                <button class="modal-close" onclick="document.getElementById('meetingDetailModal').classList.add('hidden')"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-body" id="detailBody"></div>
            <div class="modal-footer">
                <button id="detailDeleteBtn" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
                <button class="btn btn-secondary" onclick="document.getElementById('meetingDetailModal').classList.add('hidden')">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calEl = document.getElementById('calendar');
    const events = @json($events);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        firstDay: 1, // Monday
        events: events,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari',
        },
        // Disable clicking on past dates
        dateClick: function(info) {
            const clickedDate = new Date(info.dateStr);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (clickedDate < today) {
                return; // Cannot add meeting on past dates
            }

            // Pre-fill the date
            const dateStr = info.dateStr + 'T09:00';
            const endStr = info.dateStr + 'T10:00';
            document.getElementById('startAt').value = dateStr;
            document.getElementById('endAt').value = endStr;
            document.getElementById('addMeetingModal').classList.remove('hidden');
        },
        eventClick: function(info) {
            const event = info.event;
            const props = event.extendedProps;

            document.getElementById('detailTitle').innerHTML = '<i class="bi bi-calendar-event text-indigo-400 mr-2"></i>' + event.title;
            document.getElementById('detailBody').innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-sm">
                        <i class="bi bi-clock text-indigo-400 flex-shrink-0"></i>
                        <span>${new Date(event.start).toLocaleString('id-ID')} — ${new Date(event.end).toLocaleString('id-ID')}</span>
                    </div>
                    ${props.location ? `<div class="flex items-center gap-2 text-sm"><i class="bi bi-geo-alt text-blue-400"></i><span>${props.location}</span></div>` : ''}
                    ${props.description ? `<div class="text-sm text-gray-400">${props.description}</div>` : ''}
                    <div class="flex items-center gap-2">
                        <span class="badge badge-${props.status}">${props.status}</span>
                    </div>
                </div>
            `;

            document.getElementById('detailDeleteBtn').onclick = function() {
                if (!confirm('Hapus meeting ini?')) return;
                fetch(`/meetings/${event.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                }).then(() => {
                    info.event.remove();
                    document.getElementById('meetingDetailModal').classList.add('hidden');
                });
            };

            document.getElementById('meetingDetailModal').classList.remove('hidden');
        },
        // Grey out past days
        dayCellClassNames: function(arg) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return arg.date < today ? ['fc-day-past'] : [];
        },
        dayCellDidMount: function(arg) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (arg.date < today) {
                arg.el.style.pointerEvents = 'none';
                arg.el.style.opacity = '0.4';
                arg.el.style.cursor = 'not-allowed';
            }
        },
    });

    calendar.render();

    // Form submit
    document.getElementById('meetingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('/meetings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        }).then(r => r.json()).then(res => {
            if (res.success) {
                calendar.addEvent({
                    id: res.meeting.id,
                    title: res.meeting.title,
                    start: res.meeting.start_at,
                    end: res.meeting.end_at,
                    color: '#6366f1',
                });
                document.getElementById('addMeetingModal').classList.add('hidden');
                this.reset();
            }
        });
    });
});
</script>
@endpush
