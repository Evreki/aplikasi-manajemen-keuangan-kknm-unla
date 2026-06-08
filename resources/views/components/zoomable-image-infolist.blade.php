@if ($getState())
    <div class="fi-in-entry fi-in-view mb-4 w-full">
        @if ($getLabel())
            <div class="mb-2 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                {{ $getLabel() }}
            </div>
        @endif
        <div class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-2 overflow-hidden flex justify-center items-center">
            <x-zoomable-image :url="\Illuminate\Support\Facades\Storage::disk('public')->url($getState())" previewClasses="w-full object-contain cursor-pointer hover:opacity-80 transition shadow-sm rounded bg-white dark:bg-gray-800" previewStyle="max-height: 320px;" />
        </div>
    </div>
@endif
