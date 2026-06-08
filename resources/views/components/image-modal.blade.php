<div x-data="{ zoomed: false }" 
     class="w-full flex justify-center overflow-auto rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800"
     style="max-height: 85vh;"
     x-on:click="zoomed = !zoomed"
     :class="zoomed ? 'cursor-zoom-out items-start' : 'cursor-zoom-in items-center'">
    <img src="{{ $imageUrl }}" 
         alt="Gambar Bukti Pembayaran" 
         class="transition-all duration-300 ease-in-out"
         :style="zoomed ? 'max-height: none; width: 250%; max-width: 250%;' : 'max-height: 80vh; width: 100%; object-fit: contain;'">
</div>
