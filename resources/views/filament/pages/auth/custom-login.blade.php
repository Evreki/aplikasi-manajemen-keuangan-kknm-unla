<div class="flex flex-col flex-grow w-full">
<!-- Header / Branding Navigation -->
<header class="fixed top-0 w-full bg-neutral-950/60 backdrop-blur-xl flex justify-between items-center px-8 py-4 w-full z-50 shadow-2xl shadow-black/40 font-manrope tracking-tight">
    <div class="text-2xl font-black tracking-tighter text-white uppercase">
        Aplikasi Keuangan KKNM
    </div>

</header>
<!-- Main Content Area -->
<main class="flex-grow flex items-center justify-center px-4 pt-24 pb-16">
    <!-- Login Canvas -->
    <div class="relative w-full max-w-sm">
        <div class="glass-card neon-glow rounded-3xl p-6 sm:p-8 flex flex-col items-center relative z-10 transition-all duration-300 hover:shadow-[0_0_40px_rgba(0,0,0,0.8)]">
            <!-- Branding Icon -->
            <div class="mb-6 flex justify-center w-full">
                <img src="{{ asset('images/logo.png') }}" alt="Logo UNLA" class="h-20 sm:h-24 object-contain drop-shadow-[0_0_15px_rgba(56,189,248,0.4)]">
            </div>
            <!-- Headline -->
            <h1 class="font-headline text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight mb-2">Welcome Back</h1>
            <p class="text-on-surface-variant body-md text-sm mb-6 text-center">Enter your credentials to access the console</p>
            
            <!-- Form -->
            <form class="w-full space-y-4" wire:submit="authenticate">
                <!-- Email Field -->
                <div class="space-y-2">
                    <label class="font-label text-xs uppercase tracking-widest text-on-surface-variant px-1" for="email">Email</label>
                    <div class="relative group">
                        <input wire:model="data.email" class="w-full bg-surface-container-highest border-none rounded-lg py-3 lg:py-3.5 px-4 text-on-surface focus:ring-1 focus:ring-primary/40 transition-all duration-300 text-sm" id="email" placeholder="admin@example.com" type="email" required autofocus/>
                    </div>
                    @error('data.email')
                        <span class="text-error text-xs px-1 font-semibold">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="font-label text-xs uppercase tracking-widest text-on-surface-variant" for="password">Password</label>
                    </div>
                    <div class="relative group" x-data="{ showPassword: false }">
                        <input wire:model="data.password" x-bind:type="showPassword ? 'text' : 'password'" class="w-full bg-surface-container-highest border-none rounded-lg py-3 lg:py-3.5 pl-4 pr-12 text-on-surface focus:ring-1 focus:ring-primary/40 transition-all duration-300 text-sm" id="password" placeholder="••••••••" required/>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-white focus:outline-none transition-colors duration-200 py-1">
                            <span class="material-symbols-outlined select-none text-[20px] leading-none" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                    @error('data.password')
                        <span class="text-error text-xs px-1 font-semibold">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Remember Me -->
                <div class="flex items-center gap-3 px-1 mt-4">
                    <div class="relative flex items-center">
                        <input wire:model="data.remember" class="w-4 h-4 rounded bg-surface-container-highest border-outline-variant text-primary focus:ring-offset-background focus:ring-primary" id="remember" type="checkbox"/>
                    </div>
                    <label class="text-sm text-on-surface-variant cursor-pointer" for="remember">Remember this device</label>
                </div>
                
                @if ($errors->has('email') && !$errors->has('data.email'))
                    <span class="text-error text-xs px-1 mt-2 inline-block font-semibold pb-2">{{ $errors->first('email') }}</span>
                @endif
                
                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary py-3 rounded-lg text-on-primary font-headline font-bold text-base btn-primary-glow active:scale-[0.98] transition-all duration-150 mt-6 flex justify-center items-center gap-2">
                    <span wire:loading.remove wire:target="authenticate">Sign In</span>
                    <span wire:loading wire:target="authenticate">Authenticating...</span>
                </button>
            </form>
        </div>
        <!-- Subtle Grid Background for Depth -->
        <div class="absolute inset-0 -z-10 opacity-20 pointer-events-none" style="background-image: radial-gradient(#484847 0.5px, transparent 0.5px); background-size: 24px 24px;">
        </div>
    </div>
</main>
<!-- Footer Component -->
<footer class="bg-neutral-950 w-full py-8 no-line tonal-shift-bg flex flex-col items-center gap-4 w-full mt-auto">
    <div class="flex gap-8 text-neutral-600 font-inter text-xs uppercase tracking-widest">
        <span class="transition-all opacity-80 select-none">Aplikasi Keuangan KKN</span>
        <span class="transition-all opacity-80 select-none">Universitas Langlangbuana</span>
    </div>
    <div class="text-neutral-100 font-bold text-xs uppercase tracking-widest">
                © {{ date('Y') }} Aplikasi Keuangan KKN. All rights reserved.
    </div>
</footer>
<!-- Image Placeholders for Contextual Decoration -->
<div class="fixed top-0 left-0 w-full h-full -z-20 overflow-hidden pointer-events-none">
    <div class="absolute top-1/4 -right-1/4 w-[600px] h-[600px] rounded-full bg-gradient-to-br from-primary/5 to-transparent blur-[120px]"></div>
    <div class="absolute bottom-0 -left-1/4 w-[800px] h-[800px] rounded-full bg-gradient-to-tr from-secondary/5 to-transparent blur-[150px]"></div>
</div>
</div>
