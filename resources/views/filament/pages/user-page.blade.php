<div class="flex min-h-screen">

<x-panel-sidebar activePage="users" />

{{-- ======================== TOP HEADER ======================== --}}
<header class="fixed top-0 right-0 left-64 h-16 flex items-center justify-between px-8 z-40"
        style="background: rgba(14,14,14,0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(56,189,248,0.08);">
    <div>
        <h2 class="text-base font-bold text-white font-headline">Manajemen Pengguna</h2>
        <p class="text-xs mt-0.5 flex items-center gap-1.5" style="color: #6b7280;" x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour12: false }) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000)">
            <span>Kelola akses Admin dan Super Admin</span>
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

    {{-- ---- Search Bar & Actions ---- --}}
    <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-96 flex items-center gap-3 px-4 py-2.5 rounded-xl"
             style="background: rgba(255,255,255,0.04); border: 1px solid rgba(56,189,248,0.1);">
            <span class="material-symbols-outlined text-[18px]" style="color:#4b5563;">search</span>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Cari nama atau email pengguna..."
                   class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-white placeholder-gray-600 p-0" />
            @if($search)
                <button wire:click="$set('search', '')" style="color: #4b5563;" class="hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            @endif
        </div>

        <button wire:click="openCreate"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:scale-105"
                style="background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Tambah Admin
        </button>
    </div>

    {{-- ---- Table ---- --}}
    <div class="rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.02); border: 1px solid rgba(56,189,248,0.08);">
        
        <div class="overflow-x-auto" wire:loading.class="opacity-50">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background: rgba(0,0,0,0.3);">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Pengguna</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Role</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color:#4b5563;">Tanggal Terdaftar</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right whitespace-nowrap" style="color:#4b5563;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->users as $user)
                    <tr style="border-top: 1px solid rgba(56,189,248,0.05);"
                        onmouseenter="this.style.background='rgba(56,189,248,0.03)'"
                        onmouseleave="this.style.background='transparent'">
                        
                        {{-- Pengguna --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
                                     style="background: linear-gradient(135deg, rgba(14,165,233,0.2), rgba(37,99,235,0.2)); color:#38bdf8; border: 1px solid rgba(56,189,248,0.2);">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-white text-sm">{{ $user->name }}</p>
                                    <p class="text-[11px]" style="color:#9ca3af;">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'super_admin')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                      style="background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.2);">
                                    <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                    Super Admin
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                      style="background: rgba(107,114,128,0.15); color: #9ca3af; border: 1px solid rgba(107,114,128,0.2);">
                                    <span class="material-symbols-outlined text-[14px]">person</span>
                                    Admin
                                </span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xs text-white">{{ $user->created_at->format('d M Y') }}</p>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $user->id }})"
                                        title="Edit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                        style="background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>

                                @if($user->email !== 'adminkeuangan@gmail.com' && $user->id !== auth()->id())
                                <button wire:click="openDelete({{ $user->id }})"
                                        title="Hapus"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:scale-110"
                                        style="background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.15);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <span class="material-symbols-outlined text-5xl block mb-3" style="color: #1f2937;">group_off</span>
                            <p class="font-headline font-semibold text-gray-400">Tidak ada pengguna ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->users->hasPages())
        <div class="px-6 py-4" style="border-top: 1px solid rgba(56,189,248,0.06);">
            {{ $this->users->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

</main>

{{-- ======================== MODAL: FORM (CREATE/EDIT) ======================== --}}
@if($showFormModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-md rounded-2xl p-7"
         style="background: #111111; border: 1px solid rgba(56,189,248,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
        
        <div class="flex justify-between items-center mb-6 pb-4" style="border-bottom: 1px solid rgba(56,189,248,0.1);">
            <h3 class="font-headline font-bold text-xl text-white">
                {{ $editId ? 'Edit Admin' : 'Tambah Admin Baru' }}
            </h3>
            <button wire:click="closeModal" class="text-gray-500 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <form wire:submit.prevent="save" class="space-y-4">
            {{-- Name --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5 block">Nama Lengkap</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-[18px] text-gray-500">person</span>
                    <input wire:model="formData.name" type="text" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-11 pr-4 py-3 text-white outline-none focus:border-sky-500 transition-colors text-sm"
                           placeholder="Masukkan nama pengguna">
                </div>
                @error('formData.name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5 block">Email</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-[18px] text-gray-500">mail</span>
                    <input wire:model="formData.email" type="email" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-11 pr-4 py-3 text-white outline-none focus:border-sky-500 transition-colors text-sm"
                           placeholder="admin@kkn.unla.ac.id">
                </div>
                @error('formData.email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5 block">Role Akses</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-[18px] text-gray-500">verified_user</span>
                    <select wire:model="formData.role" 
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-11 pr-4 py-3 text-white outline-none focus:border-sky-500 transition-colors text-sm appearance-none">
                        <option value="admin">Admin (Akses Transaksi)</option>
                        <option value="super_admin">Super Admin (Akses Penuh)</option>
                    </select>
                </div>
                @error('formData.role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5 block">Password</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-[18px] text-gray-500">lock</span>
                    <input wire:model="formData.password" type="password" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-11 pr-4 py-3 text-white outline-none focus:border-sky-500 transition-colors text-sm"
                           placeholder="{{ $editId ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}">
                </div>
                @error('formData.password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-5" style="border-top: 1px solid rgba(56,189,248,0.1);">
                <button type="button" wire:click="closeModal"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                        style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:brightness-110 flex items-center gap-2"
                        style="background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff;">
                    <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[16px]">save</span>
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ======================== MODAL: DELETE CONFIRM ======================== --}}
@if($showDeleteModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);">
    <div class="w-full max-w-sm rounded-2xl p-7"
         style="background: #111111; border: 1px solid rgba(248,113,113,0.2); box-shadow: 0 25px 80px rgba(0,0,0,0.6);">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5"
             style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.2);">
            <span class="material-symbols-outlined text-[28px]" style="color: #f87171;">delete_forever</span>
        </div>
        <h3 class="font-headline font-bold text-xl text-white text-center mb-2">Hapus Pengguna</h3>
        <p class="text-sm text-center mb-6" style="color: #6b7280;">
            Yakin ingin menghapus pengguna ini?<br>
            <span style="color: #9ca3af;">Akses admin akun ini akan dicabut permanen.</span>
        </p>
        <div class="flex gap-3">
            <button wire:click="closeModal()"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
                    style="color: #6b7280; border: 1px solid rgba(255,255,255,0.08);">
                Batal
            </button>
            <button wire:click="deleteUser()"
                    wire:loading.attr="disabled"
                    class="flex-1 py-3 rounded-xl text-sm font-bold transition-all hover:brightness-110"
                    style="background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid rgba(248,113,113,0.3);">
                <span wire:loading.remove wire:target="deleteUser">Ya, Hapus</span>
                <span wire:loading wire:target="deleteUser">Memproses...</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
