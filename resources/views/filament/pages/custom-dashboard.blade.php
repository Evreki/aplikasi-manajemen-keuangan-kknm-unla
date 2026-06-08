<div class="flex min-h-screen">

<x-panel-sidebar activePage="dashboard" />


{{-- ======================== TOP HEADER ======================== --}}
<header class="fixed top-0 right-0 left-64 h-16 flex items-center justify-between px-8 z-40"
        style="background: rgba(14,14,14,0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(56,189,248,0.08);">
    <div>
        <h2 class="text-base font-bold text-white font-headline">Dashboard</h2>
        <p class="text-xs flex items-center gap-1.5" style="color: #6b7280;" x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour12: false }) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000)">
            <span>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            <span style="color: #38bdf8;">•</span>
            <span x-text="time" class="font-mono">{{ now()->format('H:i:s') }}</span> WIB
        </p>
    </div>
    <div class="flex items-center gap-4">
        <div title="Terhubung dengan API Web KKN" class="flex items-center gap-2 px-3 py-1.5 rounded-full cursor-help hover:bg-white/5 transition-colors" style="background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.2);">
            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
            <span class="text-[10px] font-bold uppercase tracking-wider hidden sm:block" style="color: #34d399;">Sistem Aktif</span>
        </div>
        <div class="h-5 w-px" style="background: rgba(56,189,248,0.15);"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs font-headline"
                 style="background: linear-gradient(135deg, #0ea5e9, #2563eb);">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
            <span class="text-sm font-medium hidden md:block" style="color: #d1d5db;">{{ auth()->user()->name ?? 'Admin' }}</span>
        </div>
    </div>
</header>

{{-- ======================== MAIN CONTENT ======================== --}}
<main class="ml-64 pt-24 pb-12 px-8 min-h-screen flex-1" style="background: #0e0e0e;">

    @php
        use App\Models\TransaksiKeuangan;
        $totalTransaksi = TransaksiKeuangan::count();
        $pending        = TransaksiKeuangan::where('status', 'pending')->count();
        $approved       = TransaksiKeuangan::where('status', 'approved')->count();
        $rejected       = TransaksiKeuangan::where('status', 'rejected')->count();
        $totalPemasukan = TransaksiKeuangan::where('status', 'approved')->sum('total_bayar');
        $totalPending   = TransaksiKeuangan::where('status', 'pending')->sum('total_bayar');

        // Data for Charts
        $chartDates = collect(range(6, 0))->map(fn($days) => now()->subDays($days)->format('Y-m-d'));
        $chartData = $chartDates->map(function ($date) {
            return TransaksiKeuangan::whereDate('created_at', $date)->where('status', 'approved')->sum('total_bayar');
        });
        $chartLabels = $chartDates->map(fn($date) => \Carbon\Carbon::parse($date)->isoFormat('DD MMM'));
    @endphp

    {{-- Welcome Banner --}}
    <div class="relative rounded-2xl overflow-hidden mb-8 p-8"
         style="background: linear-gradient(135deg, #111827 0%, #0c1525 50%, #111827 100%); border: 1px solid rgba(56,189,248,0.12);">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/4"
             style="background: rgba(56,189,248,0.12);"></div>
        <div class="absolute bottom-0 left-1/2 w-48 h-48 rounded-full blur-[60px] translate-y-1/2"
             style="background: rgba(37,99,235,0.1);"></div>
        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: #38bdf8;">Selamat Datang Kembali</p>
            <h1 class="text-3xl font-extrabold text-white font-headline mb-1">{{ auth()->user()->name ?? 'Admin' }} 👋</h1>
            <p class="text-sm" style="color: #93c5fd;">Pemantauan keuangan KKNM Universitas Langlangbuana · Real-time</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 xl:grid-cols-3 gap-5 mb-8">

        {{-- Card: Total Transaksi --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(56,189,248,0.35); box-shadow: 0 0 20px rgba(56,189,248,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px] group-hover:blur-[50px] transition-all"
                 style="background: rgba(56,189,248,0.15);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(56,189,248,0.08);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(56,189,248,0.2); box-shadow: 0 0 12px rgba(56,189,248,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#38bdf8;">receipt_long</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(56,189,248,0.15); color: #7dd3fc; border: 1px solid rgba(56,189,248,0.3);">Semua</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Total Transaksi</p>
            <p class="text-4xl font-extrabold font-headline text-white relative">{{ number_format($totalTransaksi) }}</p>
            <div class="mt-4 flex gap-1 items-end h-8 relative">
                @foreach([0.3,0.5,0.4,0.7,0.6,0.9,1] as $h)
                <div class="flex-1 rounded-t-sm transition-all" style="height: {{ $h * 100 }}%; background: rgba(56,189,248, {{ 0.3 + $h * 0.4 }});"></div>
                @endforeach
            </div>
        </div>

        {{-- Card: Disetujui --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(52,211,153,0.35); box-shadow: 0 0 20px rgba(52,211,153,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px]"
                 style="background: rgba(52,211,153,0.12);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(52,211,153,0.06);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(52,211,153,0.2); box-shadow: 0 0 12px rgba(52,211,153,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#34d399;">check_circle</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(52,211,153,0.15); color: #6ee7b7; border: 1px solid rgba(52,211,153,0.3);">Approved</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Transaksi Disetujui</p>
            <p class="text-4xl font-extrabold font-headline text-white relative">{{ number_format($approved) }}</p>
            <div class="mt-4 flex gap-1 items-end h-8 relative">
                @foreach([0.4,0.6,0.5,0.8,0.7,0.85,1] as $h)
                <div class="flex-1 rounded-t-sm" style="height: {{ $h * 100 }}%; background: rgba(52,211,153, {{ 0.3 + $h * 0.35 }});"></div>
                @endforeach
            </div>
        </div>

        {{-- Card: Pending --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(251,191,36,0.35); box-shadow: 0 0 20px rgba(251,191,36,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px]"
                 style="background: rgba(251,191,36,0.1);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(251,191,36,0.05);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(251,191,36,0.2); box-shadow: 0 0 12px rgba(251,191,36,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#fbbf24;">schedule</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(251,191,36,0.15); color: #fde68a; border: 1px solid rgba(251,191,36,0.3);">Pending</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Menunggu Verifikasi</p>
            <p class="text-4xl font-extrabold font-headline text-white relative">{{ number_format($pending) }}</p>
            <div class="mt-4 flex gap-1 items-end h-8 relative">
                @foreach([0.5,0.3,0.7,0.4,0.6,0.5,0.8] as $h)
                <div class="flex-1 rounded-t-sm" style="height: {{ $h * 100 }}%; background: rgba(251,191,36, {{ 0.3 + $h * 0.35 }});"></div>
                @endforeach
            </div>
        </div>

        {{-- Card: Ditolak --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(248,113,113,0.35); box-shadow: 0 0 20px rgba(248,113,113,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px]"
                 style="background: rgba(248,113,113,0.1);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(248,113,113,0.05);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(248,113,113,0.2); box-shadow: 0 0 12px rgba(248,113,113,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#f87171;">cancel</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(248,113,113,0.15); color: #fca5a5; border: 1px solid rgba(248,113,113,0.3);">Ditolak</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Transaksi Ditolak</p>
            <p class="text-4xl font-extrabold font-headline text-white relative">{{ number_format($rejected) }}</p>
            <div class="mt-4 flex gap-1 items-end h-8 relative">
                @foreach([0.2,0.4,0.3,0.5,0.2,0.4,0.3] as $h)
                <div class="flex-1 rounded-t-sm" style="height: {{ $h * 100 }}%; background: rgba(248,113,113, {{ 0.3 + $h * 0.4 }});"></div>
                @endforeach
            </div>
        </div>

        {{-- Card: Total Pemasukan --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(56,189,248,0.35); box-shadow: 0 0 20px rgba(56,189,248,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px]"
                 style="background: rgba(56,189,248,0.12);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(56,189,248,0.06);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(56,189,248,0.2); box-shadow: 0 0 12px rgba(56,189,248,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#38bdf8;">payments</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(56,189,248,0.15); color: #7dd3fc; border: 1px solid rgba(56,189,248,0.3);">Terkumpul</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Total Pemasukan</p>
            <p class="text-2xl font-extrabold font-headline text-white relative">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>

        {{-- Card: Pending Amount --}}
        <div class="relative rounded-2xl p-6 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
             style="background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(251,191,36,0.35); box-shadow: 0 0 20px rgba(251,191,36,0.08), inset 0 1px 0 rgba(255,255,255,0.1);">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[60px]"
                 style="background: rgba(251,191,36,0.1);"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full blur-[40px]"
                 style="background: rgba(251,191,36,0.05);"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgba(251,191,36,0.2); box-shadow: 0 0 12px rgba(251,191,36,0.15);">
                    <span class="material-symbols-outlined text-[20px]" style="color:#fbbf24;">account_balance_wallet</span>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                      style="background: rgba(251,191,36,0.15); color: #fde68a; border: 1px solid rgba(251,191,36,0.3);">Belum Masuk</span>
            </div>
            <p class="text-xs font-medium mb-1 relative" style="color: #94a3b8;">Pending Amount</p>
            <p class="text-2xl font-extrabold font-headline text-white relative">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
        </div>

    </div>

    {{-- ======================== CHARTS SECTION ======================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
        {{-- Chart: Trend Transaksi (Line Area) --}}
        <div class="lg:col-span-2 rounded-2xl p-6" 
             style="background: rgba(255,255,255,0.06); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(56,189,248,0.3); box-shadow: 0 0 25px rgba(56,189,248,0.06), inset 0 1px 0 rgba(255,255,255,0.08);">
            <div class="mb-2">
                <h3 class="font-headline font-bold text-lg text-white">Trend Pemasukan <span style="color: #38bdf8;">(7 Hari Terakhir)</span></h3>
                <p class="text-xs" style="color: #94a3b8;">Dinamika transaksi dan nominal uang masuk</p>
            </div>
            <div id="revenueChart" class="w-full"></div>
        </div>

        {{-- Chart: Status Sebaran (Polar/Radial) --}}
        <div class="rounded-2xl p-6"
             style="background: rgba(255,255,255,0.06); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(56,189,248,0.3); box-shadow: 0 0 25px rgba(56,189,248,0.06), inset 0 1px 0 rgba(255,255,255,0.08);">
            <div class="mb-2">
                <h3 class="font-headline font-bold text-lg text-white">Distribusi Status</h3>
                <p class="text-xs" style="color: #94a3b8;">Perbandingan proporsi keseluruhan transaksi</p>
            </div>
            
            <div id="statusChart" class="w-full flex justify-center -mt-2"></div>

            {{-- Custom HTML Legend (Guaranteed no bleeding) --}}
            <div class="flex justify-center gap-5 mt-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(52,211,153,1); box-shadow: 0 0 8px rgba(52,211,153,0.8);"></span>
                    <span class="text-[11px] font-bold tracking-wider uppercase text-white">Disetujui</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(251,191,36,1); box-shadow: 0 0 8px rgba(251,191,36,0.8);"></span>
                    <span class="text-[11px] font-bold tracking-wider uppercase text-white">Pending</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(248,113,113,1); box-shadow: 0 0 8px rgba(248,113,113,0.8);"></span>
                    <span class="text-[11px] font-bold tracking-wider uppercase text-white">Ditolak</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(56,189,248,0.25); box-shadow: 0 0 25px rgba(56,189,248,0.05), inset 0 1px 0 rgba(255,255,255,0.08);">

        {{-- Table Header --}}
        <div class="px-7 py-5 flex justify-between items-center"
             style="border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div>
                <h3 class="font-headline font-bold text-lg text-white">Transaksi Terbaru</h3>
                <p class="text-xs mt-0.5" style="color: #94a3b8;">7 data terkini dari seluruh transaksi</p>
            </div>
            <a href="{{ url('/admin/transaksi') }}"
               class="text-xs font-bold px-4 py-2 rounded-xl transition-all hover:brightness-110 flex items-center gap-1.5"
               style="background: rgba(56,189,248,0.15); color: #7dd3fc; border: 1px solid rgba(56,189,248,0.3);">
                Lihat Semua
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr style="background: rgba(255,255,255,0.03);">
                        <th class="px-7 py-4 text-[10px] font-bold uppercase tracking-widest" style="color: #64748b;">Waktu Transfer</th>
                        <th class="px-7 py-4 text-[10px] font-bold uppercase tracking-widest" style="color: #64748b;">Waktu Upload</th>
                        <th class="px-7 py-4 text-[10px] font-bold uppercase tracking-widest" style="color: #64748b;">Mahasiswa</th>
                        <th class="px-7 py-4 text-[10px] font-bold uppercase tracking-widest" style="color: #64748b;">Nominal</th>
                        <th class="px-7 py-4 text-[10px] font-bold uppercase tracking-widest" style="color: #64748b;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(TransaksiKeuangan::latest()->limit(7)->get() as $trx)
                    <tr class="transition-colors" style="border-top: 1px solid rgba(255,255,255,0.05);"
                        onmouseenter="this.style.background='rgba(255,255,255,0.04)'"
                        onmouseleave="this.style.background='transparent'">
                        <td class="px-7 py-4 text-xs font-bold" style="color: #38bdf8; background: rgba(56,189,248,0.02);">
                            @php $timeToFix = $trx->waktu_transfer ?? $trx->created_at; @endphp
                            {{ $timeToFix->format('d M Y') }}<br>
                            <span style="color: #0ea5e9;" title="Waktu Aktual Transfer">{{ $timeToFix->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-7 py-4 text-xs text-white">
                            {{ $trx->created_at->format('d M Y') }}<br>
                            <span style="color: #64748b;">{{ $trx->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-7 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
                                     style="background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff;">
                                    {{ strtoupper(substr($trx->nama_mahasiswa ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-white flex items-center gap-2">
                                        {{ $trx->nama_mahasiswa ?? '-' }}
                                        @if($trx->is_kip)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.3);">
                                                <span class="material-symbols-outlined text-[10px] mr-0.5">star</span> KIP/Kerja Sama
                                            </span>
                                        @endif
                                    </span>
                                    <p class="text-[10px] font-mono font-bold" style="color: #38bdf8; margin-top:2px;">{{ $trx->nim ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-7 py-4 text-sm font-bold text-white">
                            @if($trx->is_kip)
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.2);">
                                    JALUR KIP / KERJA SAMA
                                </span>
                            @else
                                Rp {{ number_format($trx->total_bayar ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-7 py-4">
                            @if($trx->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                      style="background: rgba(52,211,153,0.15); color: #6ee7b7; border: 1px solid rgba(52,211,153,0.3);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                    Disetujui
                                </span>
                            @elseif($trx->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                      style="background: rgba(251,191,36,0.15); color: #fde68a; border: 1px solid rgba(251,191,36,0.3);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>
                                    Pending
                                </span>
                            @elseif($trx->status === 'rejected')
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                      style="background: rgba(248,113,113,0.15); color: #fca5a5; border: 1px solid rgba(248,113,113,0.3);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="px-3 py-1 text-[10px] font-bold rounded-full uppercase" style="background: rgba(148,163,184,0.15); color: #94a3b8;">
                                    {{ $trx->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-7 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl block mb-3" style="color: #475569;">inbox</span>
                            <p class="text-sm" style="color: #64748b;">Belum ada data transaksi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>

{{-- Background ambient glow --}}
<div class="fixed top-0 right-0 pointer-events-none -z-10 w-[600px] h-[600px] rounded-full blur-[150px] opacity-30"
     style="background: radial-gradient(circle, #0ea5e9 0%, transparent 70%); top: -10%; right: -10%;"></div>
<div class="fixed pointer-events-none -z-10 w-[500px] h-[500px] rounded-full blur-[120px] opacity-20"
     style="background: radial-gradient(circle, #2563eb 0%, transparent 70%); bottom: -10%; left: 15%;"></div>
<div class="fixed pointer-events-none -z-10 w-[300px] h-[300px] rounded-full blur-[100px] opacity-15"
     style="background: radial-gradient(circle, #38bdf8 0%, transparent 70%); top: 40%; left: 40%;"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bar Chart - Trend Pemasukan (Changed from Area)
    const revOptions = {
        series: [{ name: 'Pemasukan', data: @json($chartData) }],
        chart: { type: 'bar', height: 280, toolbar: { show: false }, background: 'transparent', parentHeightOffset: 0 },
        colors: ['#38bdf8'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%',
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 1,
                opacityFrom: 0.9,
                opacityTo: 0.2,
                stops: [0, 100]
            }
        },
        dataLabels: { enabled: false },
        xaxis: { categories: @json($chartLabels), axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#94a3b8', fontFamily: 'Inter' } } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontFamily: 'Inter' }, formatter: (value) => 'Rp ' + (value/1000).toLocaleString('id-ID') + 'K' } },
        grid: { borderColor: 'rgba(56,189,248,0.1)', strokeDashArray: 4, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
        theme: { mode: 'dark' },
        tooltip: { theme: 'dark', y: { formatter: function (val) { return "Rp " + val.toLocaleString('id-ID') } } }
    };
    new ApexCharts(document.querySelector("#revenueChart"), revOptions).render();

    // Radial Bar Chart - Status Sebaran
    const approvedTrx = {{ $approved }};
    const pendingTrx = {{ $pending }};
    const rejectedTrx = {{ $rejected }};
    const totalTrx = approvedTrx + pendingTrx + rejectedTrx || 1; // Prevent div by 0

    const statOptions = {
        series: [
            Math.round((approvedTrx / totalTrx) * 100),
            Math.round((pendingTrx / totalTrx) * 100),
            Math.round((rejectedTrx / totalTrx) * 100)
        ],
        chart: { type: 'radialBar', height: 320, parentHeightOffset: 0, background: 'transparent' },
        labels: ['Disetujui', 'Pending', 'Ditolak'],
        colors: ['#34d399', '#fbbf24', '#f87171'],
        plotOptions: {
            radialBar: {
                hollow: { size: '40%', background: 'transparent' },
                track: { background: 'rgba(255,255,255,0.05)', strokeWidth: '100%', margin: 6 },
                dataLabels: {
                    name: { fontSize: '13px', color: '#94a3b8', fontFamily: 'Inter', offsetY: -10 },
                    value: { fontSize: '24px', fontWeight: 'bold', color: '#ffffff', fontFamily: 'Manrope', formatter: function (val) { return val + "%" } },
                    total: {
                        show: true,
                        label: 'Total Trans',
                        color: '#94a3b8',
                        fontSize: '12px',
                        formatter: function () {
                            return (totalTrx === 1 && approvedTrx === 0 && pendingTrx === 0 && rejectedTrx === 0) ? '0' : totalTrx;
                        }
                    }
                }
            }
        },
        stroke: { lineCap: 'round' },
        legend: { show: false }, // APEXCHARTS LEGEND DISABLED! USING CUSTOM HTML INSTEAD!
        theme: { mode: 'dark' }
    };
    new ApexCharts(document.querySelector("#statusChart"), statOptions).render();
});
</script>
