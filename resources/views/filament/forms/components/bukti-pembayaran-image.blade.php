@if($imageUrl)
<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Preview Bukti Pembayaran
    </label>
    <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
        <img 
            src="{{ $imageUrl }}" 
            alt="Bukti Pembayaran" 
            class="max-w-full h-auto rounded-lg shadow-md"
            style="max-height: 400px;"
        />
    </div>
</div>
@endif



