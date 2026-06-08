@props(['url', 'alt' => 'Gambar', 'previewClasses' => 'w-full h-auto object-contain cursor-pointer hover:opacity-80 transition', 'previewStyle' => ''])

<div x-data="{ open: false }">
    <!-- Gambar Thumbnail / Preview -->
    <img src="{{ $url }}" alt="{{ $alt }}" @click="open = true" class="{{ $previewClasses }}" style="{{ $previewStyle }}">
    
    <!-- Modal Fullscreen Overlay -->
    <template x-teleport="body">
        <div x-show="open" 
             style="display: none;" 
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
             @click.self="open = false" 
             @keydown.escape.window="open = false">
             
            <!-- Tombol Tutup -->
            <button @click="open = false" class="absolute top-4 right-4 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Kontainer Gambar Zoomable -->
            <div x-data="{ scale: 1 }" class="w-full h-full flex justify-center overflow-auto items-center" @click.self="open = false">
                <img src="{{ $url }}" 
                     alt="{{ $alt }} Full" 
                     class="transition-all duration-300 rounded-lg shadow-2xl"
                     :class="scale > 1 ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                     :style="'max-height: ' + (scale > 1 ? 'none' : '90vh') + '; max-width: ' + (scale > 1 ? '250%' : '100%') + '; width: ' + (scale > 1 ? '250%' : 'auto') + '; object-fit: contain;'"
                     @click="scale = scale === 1 ? 2.5 : 1" />
            </div>
        </div>
    </template>
</div>
