@if ($getState())
    <div class="px-3 py-2">
        <x-zoomable-image :url="\Illuminate\Support\Facades\Storage::disk('public')->url($getState())" previewClasses="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-80 shadow-sm" />
    </div>
@endif
