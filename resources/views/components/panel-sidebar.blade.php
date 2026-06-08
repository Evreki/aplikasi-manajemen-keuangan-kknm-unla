@props(['activePage' => 'dashboard'])

@php
    $pendingCount = \App\Models\TransaksiKeuangan::where('status', 'pending')->count();
@endphp

<aside class="h-screen w-64 fixed left-0 top-0 flex flex-col py-6 px-4 z-50"
       style="background: #111111; border-right: 1px solid rgba(56,189,248,0.08); box-shadow: 4px 0 30px rgba(0,0,0,0.5);">

    {{-- Logo + Brand --}}
    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="relative">
            <div class="absolute inset-0 bg-sky-400/30 rounded-xl blur-md"></div>
            <img src="{{ asset('images/logo.png') }}" alt="Logo UNLA"
                 class="relative h-11 w-11 object-contain rounded-xl">
        </div>
        <div>
            <h1 class="text-sm font-extrabold text-white tracking-tight font-headline leading-tight">App Keuangan</h1>
            <p class="text-[10px] font-medium mt-0.5" style="color: #38bdf8;">KKNM · UNLA</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1">

        {{-- Dashboard --}}
        @php $isDashboard = $activePage === 'dashboard'; @endphp
        <a href="{{ url('/admin') }}" wire:navigate
           class="group relative flex items-center gap-3 px-4 py-3 rounded-xl font-headline text-sm font-semibold transition-all"
           style="{{ $isDashboard ? 'background: linear-gradient(90deg, rgba(56,189,248,0.15) 0%, rgba(56,189,248,0.05) 100%); color: #38bdf8; box-shadow: inset 0 0 0 1px rgba(56,189,248,0.2);' : 'color: #6b7280;' }}">
            <span class="material-symbols-outlined text-[20px] transition-colors {{ $isDashboard ? '' : 'group-hover:text-sky-400' }}"
                  style="{{ $isDashboard ? 'font-variation-settings:\'FILL\' 1,\'wght\' 400' : '' }}">dashboard</span>
            Dashboard
            @if($isDashboard)
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full" style="background: #38bdf8;"></div>
            @endif
        </a>

        {{-- Transaksi --}}
        @php $isTransaksi = $activePage === 'transaksi'; @endphp
        <a href="{{ url('/admin/transaksi') }}" wire:navigate
           class="group relative flex items-center gap-3 px-4 py-3 rounded-xl font-headline text-sm font-semibold transition-all"
           style="{{ $isTransaksi ? 'background: linear-gradient(90deg, rgba(56,189,248,0.15) 0%, rgba(56,189,248,0.05) 100%); color: #38bdf8; box-shadow: inset 0 0 0 1px rgba(56,189,248,0.2);' : 'color: #6b7280;' }}">
            <span class="material-symbols-outlined text-[20px] transition-colors {{ $isTransaksi ? '' : 'group-hover:text-sky-400' }}"
                  style="{{ $isTransaksi ? 'font-variation-settings:\'FILL\' 1,\'wght\' 400' : '' }}">receipt_long</span>
            Transaksi
            @if($pendingCount > 0)
                <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                      style="background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3);">
                    {{ $pendingCount }}
                </span>
            @endif
            @if($isTransaksi)
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full" style="background: #38bdf8;"></div>
            @endif
        </a>

        {{-- Pengguna --}}
        @if(auth()->user()?->role === 'super_admin')
            @php $isUsers = $activePage === 'users'; @endphp
            <a href="{{ url('/admin/users') }}" wire:navigate
               class="group relative flex items-center gap-3 px-4 py-3 rounded-xl font-headline text-sm font-semibold transition-all"
               style="{{ $isUsers ? 'background: linear-gradient(90deg, rgba(56,189,248,0.15) 0%, rgba(56,189,248,0.05) 100%); color: #38bdf8; box-shadow: inset 0 0 0 1px rgba(56,189,248,0.2);' : 'color: #6b7280;' }}">
                <span class="material-symbols-outlined text-[20px] transition-colors {{ $isUsers ? '' : 'group-hover:text-sky-400' }}"
                      style="{{ $isUsers ? 'font-variation-settings:\'FILL\' 1,\'wght\' 400' : '' }}">group</span>
                Pengguna
                @if($isUsers)
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full" style="background: #38bdf8;"></div>
                @endif
            </a>
        @endif

    </nav>

    {{-- User Profile & Logout --}}
    <div x-data="{ showLogoutModal: false }" class="mt-auto p-3 rounded-xl flex items-center gap-3"
         style="background: rgba(56,189,248,0.05); border: 1px solid rgba(56,189,248,0.1);">
        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-sm font-headline"
             style="background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff;">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-white font-headline truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
            <p class="text-[10px] truncate" style="color: #38bdf8;">{{ auth()->user()->role ?? 'admin' }}</p>
        </div>
        <button @click="showLogoutModal = true"
                class="transition-colors hover:text-red-400" style="color: #4b5563;" title="Logout">
            <span class="material-symbols-outlined text-[18px]">logout</span>
        </button>
        <form id="sidebar-logout-form" action="{{ url('/admin/logout') }}" method="POST" class="hidden">@csrf</form>

        {{-- Logout Modal --}}
        <div x-show="showLogoutModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/75 backdrop-blur-sm" @click="showLogoutModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>
            
            <div class="relative w-full max-w-sm rounded-2xl p-7"
                 x-show="showLogoutModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 style="background: #111111; border: 1px solid rgba(248,113,113,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
                 
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5"
                     style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.2);">
                    <span class="material-symbols-outlined text-[28px]" style="color: #f87171;">logout</span>
                </div>
                <h3 class="font-headline font-bold text-xl text-white text-center mb-2">Konfirmasi Keluar</h3>
                <p class="text-sm text-center mb-6" style="color: #6b7280;">Yakin ingin mengakhiri sesi Anda?</p>
                
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false"
                            class="flex-1 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                            style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                        Tidak
                    </button>
                    <button onclick="document.getElementById('sidebar-logout-form').submit();"
                            class="flex-1 py-3 rounded-xl text-sm font-bold transition-all hover:brightness-110 flex items-center justify-center gap-1.5"
                            style="background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid rgba(248,113,113,0.3);">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>
