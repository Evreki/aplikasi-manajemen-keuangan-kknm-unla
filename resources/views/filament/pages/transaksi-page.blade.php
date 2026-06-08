<div class="flex min-h-screen">

<x-panel-sidebar activePage="transaksi" />

{{-- ======================== TOP HEADER ======================== --}}
<header class="fixed top-0 right-0 left-64 h-16 flex items-center justify-between px-8 z-40"
        style="background: rgba(14,14,14,0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(56,189,248,0.08);">
    <div>
        <h2 class="text-base font-bold text-white font-headline">Transaksi Keuangan</h2>
        <p class="text-xs mt-0.5 flex items-center gap-1.5" style="color: #6b7280;" x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour12: false }) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000)">
            <span>Verifikasi & pantau seluruh pembayaran KKNM</span>
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

    {{-- ---- Stats Bar ---- --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        @php $s = $this->stats; @endphp

        <div class="rounded-xl p-4 flex items-center gap-3 cursor-pointer transition-all hover:-translate-y-0.5"
             wire:click="setFilter('all')"
             style="background: {{ $filterStatus === 'all' ? 'rgba(56,189,248,0.15)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $filterStatus === 'all' ? 'rgba(56,189,248,0.3)' : 'rgba(255,255,255,0.06)' }};">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(56,189,248,0.1);">
                <span class="material-symbols-outlined text-[18px]" style="color:#38bdf8;">receipt_long</span>
            </div>
            <div>
                <p class="text-xl font-extrabold font-headline text-white">{{ number_format($s['total']) }}</p>
                <p class="text-[10px] uppercase tracking-wider" style="color:#6b7280;">Semua</p>
            </div>
        </div>

        <div class="rounded-xl p-4 flex items-center gap-3 cursor-pointer transition-all hover:-translate-y-0.5"
             wire:click="setFilter('pending')"
             style="background: {{ $filterStatus === 'pending' ? 'rgba(251,191,36,0.15)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $filterStatus === 'pending' ? 'rgba(251,191,36,0.3)' : 'rgba(255,255,255,0.06)' }};">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(251,191,36,0.1);">
                <span class="material-symbols-outlined text-[18px]" style="color:#fbbf24;">schedule</span>
            </div>
            <div>
                <p class="text-xl font-extrabold font-headline text-white">{{ number_format($s['pending']) }}</p>
                <p class="text-[10px] uppercase tracking-wider" style="color:#6b7280;">Pending</p>
            </div>
        </div>

        <div class="rounded-xl p-4 flex items-center gap-3 cursor-pointer transition-all hover:-translate-y-0.5"
             wire:click="setFilter('approved')"
             style="background: {{ $filterStatus === 'approved' ? 'rgba(52,211,153,0.15)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $filterStatus === 'approved' ? 'rgba(52,211,153,0.3)' : 'rgba(255,255,255,0.06)' }};">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(52,211,153,0.1);">
                <span class="material-symbols-outlined text-[18px]" style="color:#34d399;">check_circle</span>
            </div>
            <div>
                <p class="text-xl font-extrabold font-headline text-white">{{ number_format($s['approved']) }}</p>
                <p class="text-[10px] uppercase tracking-wider" style="color:#6b7280;">Disetujui</p>
            </div>
        </div>

        <div class="rounded-xl p-4 flex items-center gap-3 cursor-pointer transition-all hover:-translate-y-0.5"
             wire:click="setFilter('rejected')"
             style="background: {{ $filterStatus === 'rejected' ? 'rgba(248,113,113,0.15)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $filterStatus === 'rejected' ? 'rgba(248,113,113,0.3)' : 'rgba(255,255,255,0.06)' }};">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(248,113,113,0.1);">
                <span class="material-symbols-outlined text-[18px]" style="color:#f87171;">cancel</span>
            </div>
            <div>
                <p class="text-xl font-extrabold font-headline text-white">{{ number_format($s['rejected']) }}</p>
                <p class="text-[10px] uppercase tracking-wider" style="color:#6b7280;">Ditolak</p>
            </div>
        </div>

        <div class="rounded-xl p-4 flex items-center gap-3 col-span-2 md:col-span-1"
             style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(56,189,248,0.1);">
                <span class="material-symbols-outlined text-[18px]" style="color:#38bdf8;">payments</span>
            </div>
            <div>
                <p class="text-base font-extrabold font-headline text-white leading-tight">
                    Rp {{ number_format($s['pemasukan'], 0, ',', '.') }}
                </p>
                <p class="text-[10px] uppercase tracking-wider" style="color:#6b7280;">Pemasukan</p>
            </div>
        </div>
    </div>

    {{-- ---- Search & Export Bar ---- --}}
    <div class="mb-5 flex flex-col md:flex-row gap-3">
        {{-- Search --}}
        <div class="flex-1 flex items-center gap-3 px-4 py-2.5 rounded-xl"
             style="background: rgba(255,255,255,0.04); border: 1px solid rgba(56,189,248,0.1);">
            <span class="material-symbols-outlined text-[18px]" style="color:#4b5563;">search</span>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Cari NIM, nama mahasiswa, atau nomor WA..."
                   class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-white placeholder-gray-600 p-0" />
            @if($search)
                <button wire:click="$set('search', '')" style="color: #4b5563;" class="hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            @endif
        </div>

        {{-- Filter Periode (Tahun) --}}
        <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl"
             style="background: rgba(255,255,255,0.04); border: 1px solid rgba(56,189,248,0.1);">
            <span class="material-symbols-outlined text-[18px]" style="color:#38bdf8;">filter_alt</span>
            <select wire:model.live="filterYear"
                    class="bg-transparent border-none focus:ring-0 text-sm text-white p-0 pr-8 cursor-pointer font-medium outline-none"
                    style="box-shadow: none;">
                <option value="all" class="bg-[#111111] text-white">Semua Periode</option>
                @foreach($this->availableYears as $year)
                    <option value="{{ $year }}" class="bg-[#111111] text-white">Tahun {{ $year }}</option>
                @endforeach
            </select>
        </div>

        {{-- Export Actions --}}
        <div class="flex items-center gap-2">
            <button wire:click="exportPdf"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-white/10"
                    style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);">
                <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                PDF
            </button>
            <button wire:click="exportCsv"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-white/10"
                    style="background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2);">
                <span class="material-symbols-outlined text-[16px]">csv</span>
                CSV
            </button>
            <button wire:click="exportExcel"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-white/10"
                    style="background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2);">
                <span class="material-symbols-outlined text-[16px]">table_view</span>
                Excel
            </button>
        </div>
    </div>

    {{-- ---- Table ---- --}}
    <div class="rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.02); border: 1px solid rgba(56,189,248,0.08);">

        {{-- Table header --}}
        <div class="px-6 py-4 flex justify-between items-center" style="border-bottom: 1px solid rgba(56,189,248,0.06);">
            <div class="flex items-center gap-3">
                <h3 class="font-headline font-bold text-white">Daftar Transaksi</h3>
                @if($filterStatus !== 'all')
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                          style="background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.2);">
                        Status: {{ $filterStatus }}
                    </span>
                @endif
                @if($filterYear !== 'all')
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                          style="background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.2);">
                        Periode: {{ $filterYear }}
                    </span>
                @endif
            </div>
            <div wire:loading class="flex items-center gap-2 text-xs" style="color: #38bdf8;">
                <span class="material-symbols-outlined text-[16px] animate-spin">autorenew</span>
                Memuat...
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.class="opacity-50">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background: rgba(0,0,0,0.3);">
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#38bdf8;">Waktu Transfer</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Waktu Upload</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Mahasiswa</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">No WA</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Bukti</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Nominal</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Status</th>
                        <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-right whitespace-nowrap" style="color:#4b5563;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->transaksi as $trx)
                    <tr style="border-top: 1px solid rgba(56,189,248,0.05);"
                        onmouseenter="this.style.background='rgba(56,189,248,0.03)'"
                        onmouseleave="this.style.background='transparent'">

                        {{-- Waktu Transfer (Input Siswa) --}}
                        <td class="px-6 py-4 whitespace-nowrap" style="background: rgba(56,189,248,0.02);">
                            @php $timeToFix = $trx->waktu_transfer ?? $trx->created_at; @endphp
                            <p class="text-xs font-bold" style="color:#38bdf8;">{{ $timeToFix->format('d M Y') }}</p>
                            <p class="text-[10px]" style="color:#0ea5e9;" title="Waktu Aktual Transfer">{{ $timeToFix->format('H:i') }} WIB</p>
                        </td>

                        {{-- Waktu Upload (Sistem Keuangan) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xs font-medium text-white">{{ $trx->created_at->format('d M Y') }}</p>
                            <p class="text-[10px]" style="color:#4b5563;">{{ $trx->created_at->format('H:i') }} WIB</p>
                        </td>

                        {{-- Mahasiswa --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
                                     style="background: linear-gradient(135deg, #0ea5e9, #2563eb); color:#fff; min-width:36px;">
                                    {{ strtoupper(substr($trx->nama_mahasiswa ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-white text-sm leading-tight flex items-center gap-2">
                                        {{ $trx->nama_mahasiswa ?? '-' }}
                                        @if($trx->is_kip)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.3);" title="Jalur Kartu Indonesia Pintar">
                                                <span class="material-symbols-outlined text-[10px] mr-0.5">star</span> KIP/Kerja Sama
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] font-mono font-bold" style="color:#38bdf8;">{{ $trx->nim }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- No WA --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xs font-mono" style="color:#9ca3af;">{{ $trx->no_telepon ?? '-' }}</p>
                        </td>

                        {{-- Bukti --}}
                        <td class="px-6 py-4">
                            @if($trx->bukti_pembayaran_path)
                                <button wire:click="openView({{ $trx->id }})"
                                        class="group relative overflow-hidden rounded-lg transition-all hover:scale-105"
                                        style="width: 48px; height: 48px;">
                                    <img src="{{ asset('storage/' . $trx->bukti_pembayaran_path) }}"
                                         alt="Bukti"
                                         class="w-full h-full object-cover"
                                         onerror="this.parentNode.innerHTML='<div style=\'width:48px;height:48px;background:rgba(56,189,248,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;\'><span style=\'color:#4b5563;font-size:18px;\' class=\'material-symbols-outlined\'>broken_image</span></div>'">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                         style="background: rgba(0,0,0,0.5);">
                                        <span class="material-symbols-outlined text-white text-[16px]">zoom_in</span>
                                    </div>
                                </button>
                            @else
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                     style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.1);">
                                    <span class="material-symbols-outlined text-[16px]" style="color:#374151;">image_not_supported</span>
                                </div>
                            @endif
                        </td>

                        {{-- Nominal --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trx->is_kip)
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.2);">
                                    JALUR KIP / KERJA SAMA
                                </span>
                            @else
                                <p class="text-sm font-bold text-white font-headline">
                                    Rp {{ number_format($trx->total_bayar ?? 0, 0, ',', '.') }}
                                </p>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($trx->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider whitespace-nowrap"
                                      style="background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.2);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                    Disetujui
                                </span>
                            @elseif($trx->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider whitespace-nowrap"
                                      style="background: rgba(251,191,36,0.1); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>
                                    Pending
                                </span>
                            @elseif($trx->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider whitespace-nowrap"
                                      style="background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.2);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                    Ditolak
                                </span>
                            @endif

                            @if($trx->verified_by)
                                <p class="text-[9px] text-gray-500 mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">person</span>
                                    {{ $trx->verifier->name ?? 'Admin' }}
                                </p>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Detail --}}
                                <button wire:click="openView({{ $trx->id }})"
                                        title="Lihat Detail"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                        style="background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </button>

                                {{-- Edit --}}
                                <button wire:click="openEdit({{ $trx->id }})"
                                   title="Edit"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                   style="background: rgba(148,163,184,0.1); color: #94a3b8; border: 1px solid rgba(148,163,184,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                
                                {{-- Approve --}}
                                @if($trx->status !== 'approved')
                                <button wire:click="openApprove({{ $trx->id }})"
                                        title="Setujui & Kirim WA"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                        style="background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                </button>
                                @endif

                                {{-- Reject --}}
                                @if($trx->status !== 'rejected')
                                <button wire:click="openReject({{ $trx->id }})"
                                        title="Tolak"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                        style="background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <span class="material-symbols-outlined text-5xl block mb-3" style="color: #1f2937;">inbox</span>
                            <p class="font-headline font-semibold" style="color: #374151;">Tidak ada transaksi ditemukan</p>
                            <p class="text-xs mt-1" style="color: #1f2937;">
                                @if($search) Coba ubah kata kunci pencarian @else Belum ada data @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->transaksi->hasPages())
        <div class="px-6 py-4" style="border-top: 1px solid rgba(56,189,248,0.06);">
            {{ $this->transaksi->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

</main>

{{-- ======================== MODAL: VIEW DETAIL ======================== --}}
@if($showViewModal && $this->selectedRecord)
@php $r = $this->selectedRecord; @endphp
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-2xl rounded-2xl overflow-hidden flex flex-col max-h-[90vh]"
         style="background: #111111; border: 1px solid rgba(56,189,248,0.15); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-7 py-5" style="border-bottom: 1px solid rgba(56,189,248,0.08);">
            <div>
                <h3 class="font-headline font-bold text-lg text-white">Detail Transaksi</h3>
                <p class="text-xs mt-0.5" style="color: #38bdf8;">#ID {{ $r->id }} · {{ $r->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <button wire:click="closeModals()" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-white/10" style="color: #6b7280;">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="overflow-y-auto flex-1 p-7 space-y-6">

            {{-- Bukti Pembayaran / KIP --}}
            @if($r->bukti_pembayaran_path)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest mb-3" style="color: #4b5563;">{{ $r->is_kip ? 'Dokumen Kartu KIP / Kerja Sama' : 'Bukti Pembayaran' }}</p>
                <div class="rounded-xl overflow-hidden" style="border: 1px solid rgba(56,189,248,0.1);">
                    <x-zoomable-image :url="asset('storage/' . $r->bukti_pembayaran_path)" alt="Bukti Pembayaran" previewClasses="w-full object-contain cursor-zoom-in transition-all duration-300" previewStyle="max-height: 20rem; background: #000;" />
                </div>
            </div>
            @endif

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['label' => 'Jalur Pendaftaran',     'value' => $r->is_kip ? 'Jalur KIP / Kerja Sama' : 'Jalur Reguler', 'icon' => 'school'],
                    ['label' => 'Waktu Transfer/Upload', 'value' => ($r->waktu_transfer ? $r->waktu_transfer->format('d M Y, H:i') : $r->created_at->format('d M Y, H:i')) . ' WIB', 'icon' => 'schedule'],
                    ['label' => 'Nama Mahasiswa',  'value' => $r->nama_mahasiswa,  'icon' => 'person'],
                    ['label' => 'NIM',              'value' => $r->nim,             'icon' => 'badge'],
                    ['label' => 'No WhatsApp',      'value' => $r->no_telepon ?: '-','icon' => 'call'],
                    ['label' => 'Keterangan/Presensi','value' => $r->keterangan_mahasiswa ?: '-','icon' => 'description'],
                    ['label' => 'ID Pembayaran KKN','value' => '#' . $r->kkn_pembayaran_id, 'icon' => 'tag'],
                    ['label' => 'Nominal Tagihan',    'value' => 'Rp ' . number_format($r->total_bayar ?? 0, 0, ',', '.'), 'icon' => 'payments'],
                    ['label' => 'Waktu Submit Sistem',   'value' => $r->created_at->format('d M Y, H:i') . ' WIB', 'icon' => 'cloud_upload'],
                ] as $info)
                <div class="rounded-xl p-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[14px]" style="color: #38bdf8;">{{ $info['icon'] }}</span>
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: #4b5563;">{{ $info['label'] }}</p>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ $info['value'] }}</p>
                </div>
                @endforeach

                {{-- Status -- full width --}}
                <div class="col-span-2 rounded-xl p-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[14px]" style="color: #38bdf8;">info</span>
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: #4b5563;">Status</p>
                    </div>
                    @if($r->status === 'approved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full uppercase tracking-wider"
                              style="background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.2);">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> Disetujui
                        </span>
                    @elseif($r->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full uppercase tracking-wider"
                              style="background: rgba(251,191,36,0.1); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2);">
                            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block animate-pulse"></span> Menunggu Verifikasi
                        </span>
                    @elseif($r->status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full uppercase tracking-wider"
                              style="background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.2);">
                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span> Ditolak
                        </span>
                    @endif

                    @if($r->verified_by)
                        <div class="mt-4 pt-4 flex flex-col gap-2" style="border-top: 1px solid rgba(255,255,255,0.05);">
                            <p class="text-xs font-semibold text-gray-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">how_to_reg</span>
                                Diverifikasi oleh: <span class="text-white">{{ $r->verifier->name ?? 'Admin' }}</span>
                            </p>
                            @if($r->status === 'rejected' && $r->alasan_penolakan)
                                <div class="p-3.5 rounded-xl mt-1" style="background: rgba(248,113,113,0.08); border: 1px dashed rgba(248,113,113,0.25);">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-red-500 mb-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        Alasan Penolakan
                                    </p>
                                    <p class="text-sm text-red-200">{{ $r->alasan_penolakan }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Footer — Actions for pending --}}
        @if($r->status === 'pending')
        <div class="px-7 py-5 flex justify-end gap-3" style="border-top: 1px solid rgba(56,189,248,0.08);">
            <button wire:click="closeModals()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                Tutup
            </button>
            <button wire:click="openReject({{ $r->id }})"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:brightness-110"
                    style="background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.25);">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">cancel</span>
                Tolak
            </button>
            <button wire:click="openApprove({{ $r->id }})"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:brightness-110"
                    style="background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.25);">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">check_circle</span>
                Setujui & Kirim WA
            </button>
        </div>
        @else
        <div class="px-7 py-5 flex justify-end" style="border-top: 1px solid rgba(56,189,248,0.08);">
            <button wire:click="closeModals()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #9ca3af; border: 1px solid rgba(255,255,255,0.08);">
                Tutup
            </button>
        </div>
        @endif
    </div>
</div>
@endif

{{-- ======================== MODAL: EDIT ======================== --}}
@if($showEditModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-lg rounded-2xl p-7"
         style="background: #111111; border: 1px solid rgba(56,189,248,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
        <h3 class="font-headline font-bold text-xl text-white mb-6 border-b pb-4" style="border-color: rgba(56,189,248,0.1);">Edit Transaksi</h3>
        
        <div class="space-y-4">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">ID Pembayaran KKN</label>
                <input wire:model="editData.kkn_pembayaran_id" type="number" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                @error('editData.kkn_pembayaran_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">NIM</label>
                    <input wire:model="editData.nim" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                    @error('editData.nim') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Total Bayar</label>
                    <input wire:model="editData.total_bayar" type="number" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                    @error('editData.total_bayar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Nama Mahasiswa</label>
                <input wire:model="editData.nama_mahasiswa" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                @error('editData.nama_mahasiswa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">No Telepon (WA)</label>
                    <input wire:model="editData.no_telepon" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                    @error('editData.no_telepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Status</label>
                    <select wire:model="editData.status" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-sky-500 text-sm">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-5" style="border-top: 1px solid rgba(56,189,248,0.1);">
            <button wire:click="closeModals()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                Batal
            </button>
            <button wire:click="saveEdit()"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:brightness-110 flex items-center"
                    style="background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.25);">
                <span wire:loading.remove wire:target="saveEdit" class="material-symbols-outlined text-[16px] align-middle mr-1">save</span>
                <span wire:loading.remove wire:target="saveEdit">Simpan</span>
                <span wire:loading wire:target="saveEdit">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ======================== MODAL: APPROVE CONFIRM ======================== --}}
@if($showApproveModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-sm rounded-2xl p-7"
         style="background: #111111; border: 1px solid rgba(52,211,153,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5"
             style="background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.2);">
            <span class="material-symbols-outlined text-[28px]" style="color: #34d399;">check_circle</span>
        </div>
        <h3 class="font-headline font-bold text-xl text-white text-center mb-2">Verifikasi Pembayaran</h3>
        <p class="text-sm text-center mb-6" style="color: #6b7280;">
            Yakin menyetujui pembayaran ini?<br>
            <span style="color: #9ca3af;">Notifikasi WhatsApp akan otomatis dikirim ke mahasiswa.</span>
        </p>
        <div class="flex gap-3">
            <button wire:click="closeModals()"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                Batal
            </button>
            <button wire:click="doApprove()"
                    wire:loading.attr="disabled"
                    class="flex-1 py-3 rounded-xl text-sm font-bold transition-all hover:brightness-110"
                    style="background: rgba(52,211,153,0.2); color: #34d399; border: 1px solid rgba(52,211,153,0.3);">
                <span wire:loading.remove wire:target="doApprove">Ya, Setujui</span>
                <span wire:loading wire:target="doApprove">Memproses...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ======================== MODAL: REJECT ======================== --}}
@if($showRejectModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-sm rounded-2xl p-7"
         style="background: #111111; border: 1px solid rgba(248,113,113,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5"
             style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.2);">
            <span class="material-symbols-outlined text-[28px]" style="color: #f87171;">cancel</span>
        </div>
        <h3 class="font-headline font-bold text-xl text-white text-center mb-2">Tolak Pembayaran</h3>
        <p class="text-sm text-center mb-5" style="color: #6b7280;">Berikan alasan penolakan. Notifikasi WA akan dikirim ke mahasiswa.</p>

        <div class="mb-2">
            <label class="text-[10px] font-bold uppercase tracking-widest mb-2 block" style="color: #4b5563;">Alasan Penolakan</label>
            <textarea wire:model="alasanTolak"
                      rows="3"
                      placeholder="Contoh: Foto buram, nominal kurang, bukan bukti transfer."
                      class="w-full rounded-xl px-4 py-3 text-sm text-white resize-none focus:ring-1"
                      style="background: rgba(255,255,255,0.04); border: 1px solid rgba(248,113,113,0.2); focus:ring-color: #f87171;"></textarea>
            @error('alasanTolak')
                <p class="text-xs mt-1.5" style="color: #f87171;">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 mt-5">
            <button wire:click="closeModals()"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                Batal
            </button>
            <button wire:click="doReject()"
                    wire:loading.attr="disabled"
                    class="flex-1 py-3 rounded-xl text-sm font-bold transition-all hover:brightness-110"
                    style="background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid rgba(248,113,113,0.3);">
                <span wire:loading.remove wire:target="doReject">Tolak & Kirim WA</span>
                <span wire:loading wire:target="doReject">Memproses...</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
