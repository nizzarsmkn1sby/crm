<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — WebCare CRM</title>
    <meta name="description" content="WebCare CRM - Sistem Manajemen Hubungan Pelanggan">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Custom CRM Styles --}}
    <link rel="stylesheet" href="{{ asset('css/crm.css') }}">

    @stack('styles')
</head>
<body class="crm-body" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar') !== 'false' }" @beforeunload.window="localStorage.setItem('sidebar', sidebarExpanded)">

    {{-- Sidebar --}}
    <aside class="crm-sidebar" :class="{ 'sidebar-collapsed': !sidebarExpanded, 'sidebar-mobile-open': sidebarOpen }">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <span class="logo-text" x-show="sidebarExpanded" x-transition>WebCare CRM</span>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">UTAMA</span>
                <a href="{{ route('dashboard') }}" title="Dashboard" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Dashboard</span>
                </a>
                <a href="{{ route('pipeline.index') }}" title="Pipeline" class="nav-item {{ request()->routeIs('pipeline.*') ? 'active' : '' }}">
                    <i class="bi bi-kanban-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Pipeline</span>
                </a>
            </div>

            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">CRM</span>
                <a href="{{ route('leads.index') }}" title="Lead" class="nav-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                    <i class="bi bi-person-plus-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Lead</span>
                    @php $newLeads = \App\Models\Lead::where('status','new')->count() @endphp
                    @if($newLeads > 0)
                        <span class="nav-badge" x-show="sidebarExpanded">{{ $newLeads }}</span>
                    @endif
                </a>
                <a href="{{ route('contacts.index') }}" title="Kontak" class="nav-item {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Kontak</span>
                </a>
                <a href="{{ route('companies.index') }}" title="Perusahaan" class="nav-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                    <i class="bi bi-building nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Perusahaan</span>
                </a>
                <a href="{{ route('deals.index') }}" title="Deal" class="nav-item {{ request()->routeIs('deals.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Deal</span>
                </a>
            </div>

            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">AKTIVITAS</span>
                <a href="{{ route('tasks.index') }}" title="Task" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="bi bi-check2-square nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Task</span>
                    @php $overdueTasks = \App\Models\Task::where('status','pending')->where('due_date','<',now())->count() @endphp
                    @if($overdueTasks > 0)
                        <span class="nav-badge nav-badge-danger" x-show="sidebarExpanded">{{ $overdueTasks }}</span>
                    @endif
                </a>
                <a href="{{ route('meetings.index') }}" title="Kalender" class="nav-item {{ request()->routeIs('meetings.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3 nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Kalender</span>
                </a>
                <a href="{{ route('activities.index') }}" title="Aktivitas" class="nav-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                    <i class="bi bi-activity nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Aktivitas</span>
                </a>
                <a href="{{ route('documents.index') }}" title="Dokumen" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Dokumen</span>
                </a>
            </div>

            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">MARKETING</span>
                <a href="{{ route('campaigns.index') }}" title="Kampanye" class="nav-item {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Kampanye</span>
                </a>
                <a href="{{ route('automation.index') }}" title="Otomasi" class="nav-item {{ request()->routeIs('automation.*') ? 'active' : '' }}">
                    <i class="bi bi-robot nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Otomasi</span>
                </a>
            </div>

            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">ANALITIK</span>
                <a href="{{ route('reports.index') }}" title="Laporan" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Laporan</span>
                </a>
            </div>

            <div class="nav-section">
                <span class="nav-section-title" x-show="sidebarExpanded">SISTEM</span>
                <a href="{{ route('settings.index') }}" title="Pengaturan" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill nav-icon"></i>
                    <span class="nav-label" x-show="sidebarExpanded">Pengaturan</span>
                </a>
            </div>
        </nav>

        {{-- User profile at bottom --}}
        <div class="sidebar-footer">
            <img src="{{ auth()->check() ? auth()->user()->avatar_url : 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff' }}" class="sidebar-avatar" alt="Avatar">
            <div class="sidebar-footer-info" x-show="sidebarExpanded" x-transition.opacity.duration.200ms>
                <p class="text-sm font-semibold text-white truncate" style="max-width:140px;">{{ auth()->user()?->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()?->role_label }}</p>
            </div>
        </div>

        {{-- Toggle Bar — selalu visible, tidak absolut --}}
        <div class="sidebar-toggle-bar">
            <button class="sidebar-toggle-btn" @click.stop="sidebarExpanded = !sidebarExpanded" :title="sidebarExpanded ? 'Perkecil sidebar' : 'Perbesar sidebar'">
                <div class="sidebar-toggle-icon">
                    <i class="bi" :class="sidebarExpanded ? 'bi-layout-sidebar-reverse' : 'bi-layout-sidebar'"></i>
                </div>
                <span class="sidebar-toggle-label">
                    <span x-show="sidebarExpanded" x-transition.opacity>Perkecil Sidebar</span>
                </span>
            </button>
        </div>
    </aside>

    {{-- Mobile sidebar overlay --}}
    <div class="sidebar-overlay" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity></div>

    {{-- Main Content --}}
    <div class="crm-main" :class="{ 'main-sidebar-collapsed': !sidebarExpanded }">
        {{-- Top Header --}}
        <header class="crm-header">
            <div class="header-left">
                <button class="mobile-menu-btn lg:hidden" @click="sidebarOpen = !sidebarOpen">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <div class="header-breadcrumb">
                    @yield('breadcrumb', '<span class="text-gray-400 text-sm">Dashboard</span>')
                </div>
            </div>
            <div class="header-right">
                {{-- Quick Create --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click.stop="open = !open" class="btn btn-primary btn-sm flex items-center gap-1">
                        <i class="bi bi-plus-circle"></i>
                        <span class="hidden md:inline">Tambah</span>
                        <i class="bi bi-chevron-down text-[10px] opacity-60"></i>
                    </button>
                    <div class="user-dropdown" x-show="open" @click.outside="open = false" x-transition style="width: 180px;">
                        <a href="{{ route('leads.create') }}" class="dropdown-item"><i class="bi bi-person-plus text-indigo-400"></i> Lead Baru</a>
                        <a href="{{ route('deals.index') }}" class="dropdown-item"><i class="bi bi-bag-plus text-green-400"></i> Deal Baru</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('tasks.index') }}" class="dropdown-item"><i class="bi bi-check2-square text-orange-400"></i> Buat Task</a>
                        <a href="{{ route('meetings.index') }}" class="dropdown-item"><i class="bi bi-calendar-plus text-blue-400"></i> Jadwal Meeting</a>
                    </div>
                </div>

                {{-- Global Search --}}
                <div class="relative" x-data="{
                    searchOpen: false, results: [], searching: false, q: '',
                    async quickSearch() {
                        if (this.q.length < 2) { this.results = []; return; }
                        this.searching = true;
                        try {
                            const res = await fetch(`{{ route('search.quick') }}?q=${encodeURIComponent(this.q)}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
                            this.results = await res.json();
                        } catch(e) { this.results = []; }
                        this.searching = false;
                    }
                }">
                    <button @click.stop="searchOpen = !searchOpen" class="header-icon-btn" title="Cari (Ctrl+K)" id="global-search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                    <div class="search-popup" x-show="searchOpen" @click.outside="searchOpen = false" x-transition style="width:420px;">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="bi bi-search text-gray-400 flex-shrink-0"></i>
                            <input type="text" x-model="q"
                                @input.debounce.350ms="quickSearch()"
                                @keydown.enter.prevent="window.location.href='{{ route('search') }}?q=' + encodeURIComponent(q)"
                                @keydown.escape="searchOpen = false"
                                placeholder="Cari lead, kontak, deal, perusahaan..."
                                class="search-input-popup" autofocus id="global-search-input">
                            <a :href="'{{ route('search') }}?q=' + encodeURIComponent(q)" class="btn btn-primary btn-sm flex-shrink-0">Cari</a>
                        </div>
                        {{-- Quick Results --}}
                        <template x-if="results.length > 0">
                            <div class="mt-2 border-t border-gray-700/50 pt-2">
                                <template x-for="r in results" :key="r.url">
                                    <a :href="r.url" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 transition-all">
                                        <i :class="'bi ' + r.icon + ' ' + r.color + ' text-base'"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-white font-medium truncate" x-text="r.label"></p>
                                            <p class="text-xs text-gray-400 truncate" x-text="r.sub"></p>
                                        </div>
                                        <span class="text-xs text-gray-600 capitalize" x-text="r.type"></span>
                                    </a>
                                </template>
                                <a :href="'{{ route('search') }}?q=' + encodeURIComponent(q)" class="block text-center text-xs text-indigo-400 hover:text-indigo-300 mt-2 py-1">Lihat semua hasil →</a>
                            </div>
                        </template>
                        <template x-if="searching">
                            <div class="text-center py-3 text-gray-500 text-sm"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Mencari...</div>
                        </template>
                        <template x-if="!searching && q.length >= 2 && results.length === 0">
                            <div class="text-center py-3 text-gray-500 text-sm">Tidak ada hasil untuk "<span x-text="q" class="text-white"></span>"</div>
                        </template>
                        <div class="mt-2 pt-2 border-t border-gray-700/50 text-xs text-gray-600 flex gap-4">
                            <span><kbd class="bg-gray-800 px-1 rounded">Enter</kbd> Cari semua</span>
                            <span><kbd class="bg-gray-800 px-1 rounded">Esc</kbd> Tutup</span>
                        </div>
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false, unread: {{ auth()->user()?->unreadNotifications->count() ?? 0 }} }" x-init="setInterval(() => fetchNotifCount(), 30000)">
                    <button @click.stop="open = !open" class="header-icon-btn relative" title="Notifikasi">
                        <i class="bi bi-bell-fill"></i>
                        <span class="notif-badge" x-show="unread > 0" x-text="unread > 9 ? '9+' : unread"></span>
                    </button>
                    <div class="notif-dropdown" x-show="open" @click.outside="open = false" x-transition>
                        <div class="notif-header">
                            <span class="font-semibold">Notifikasi</span>
                            <div x-show="unread > 0">
                                <form action="{{ route('notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-400 hover:text-indigo-300">Tandai semua dibaca</button>
                                </form>
                            </div>
                        </div>
                        @php $notifications = auth()->user()?->notifications->take(5) ?? collect() @endphp
                        @forelse($notifications as $notif)
                            <div class="notif-item {{ $notif->read_at ? 'opacity-60' : '' }}">
                                <div class="flex gap-2">
                                    <i class="bi bi-info-circle text-indigo-400 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-sm">{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
                                        <span class="text-xs text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-500">
                                <i class="bi bi-bell-slash text-3xl block mb-2"></i>
                                <p class="text-sm">Belum ada notifikasi</p>
                            </div>
                        @endforelse
                        <a href="{{ route('notifications.index') }}" class="notif-footer">Lihat semua →</a>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click.stop="open = !open" class="user-btn">
                        <img src="{{ auth()->user()?->avatar_url }}" alt="Avatar" class="user-avatar">
                        <span class="hidden sm:block text-sm font-medium text-gray-200 max-w-[120px] truncate">{{ auth()->user()?->name }}</span>
                        <i class="bi bi-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div class="user-dropdown" x-show="open" @click.outside="open = false" x-transition>
                        <div class="user-dropdown-header">
                            <img src="{{ auth()->user()?->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <p class="font-semibold text-white text-sm">{{ auth()->user()?->name }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()?->email }}</p>
                                <span class="role-badge">{{ auth()->user()?->role_label }}</span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="bi bi-person-circle"></i> Profil Saya</a>
                        <a href="{{ route('settings.index') }}" class="dropdown-item"><i class="bi bi-gear"></i> Pengaturan</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item w-full text-red-400 hover:text-red-300">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="crm-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert-toast alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition.origin.top.right>
                    <i class="bi bi-check-circle-fill text-emerald-400 text-xl flex-shrink-0"></i>
                    <span class="flex-1 text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="text-gray-400 hover:text-white ml-2"><i class="bi bi-x-lg"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-toast alert-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.origin.top.right>
                    <i class="bi bi-exclamation-circle-fill text-red-400 text-xl flex-shrink-0"></i>
                    <span class="flex-1 text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="text-gray-400 hover:text-white ml-2"><i class="bi bi-x-lg"></i></button>
                </div>
            @endif

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert-toast alert-error" x-data="{ show: true }" x-show="show">
                    <i class="bi bi-exclamation-triangle-fill text-red-400 text-xl flex-shrink-0"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold mb-1">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-white ml-2"><i class="bi bi-x-lg"></i></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
<script>
// Ctrl+K shortcut untuk global search
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('global-search-btn')?.click();
        setTimeout(() => document.getElementById('global-search-input')?.focus(), 100);
    }
});

// Quick search AJAX — dipanggil dari AlpineJS
function quickSearch() {
    const comp = Alpine.$data(document.querySelector('[x-data*="quickSearch"]') || document.querySelector('.relative[x-data*="results"]'));
    // Fungsi dipanggil dari Alpine context
}

// Alpine global helper for search
document.addEventListener('alpine:init', () => {
    Alpine.data('globalSearch', () => ({
        searchOpen: false, results: [], searching: false, q: '',
        async quickSearch() {
            if (this.q.length < 2) { this.results = []; return; }
            this.searching = true;
            try {
                const res = await fetch(`{{ route('search.quick') }}?q=${encodeURIComponent(this.q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.results = await res.json();
            } catch(e) { this.results = []; }
            this.searching = false;
        }
    }));
});

// Polling notifikasi
async function fetchNotifCount() {
    try {
        const res = await fetch('{{ route('notifications.index') }}?count=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (res.ok) {
            const data = await res.json();
            // Update semua Alpine unread counter
            document.querySelectorAll('[x-data*="unread"]').forEach(el => {
                const comp = Alpine.$data(el);
                if (comp && 'unread' in comp) comp.unread = data.unread ?? comp.unread;
            });
        }
    } catch(e) {}
}
</script>
</body>
</html>
