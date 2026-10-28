{{-- The x-tooltip attribute for a decoration's root element. Include it inside the tag, passing ['tooltip' => $getTooltip()]. --}}
@if (filled($tooltip))
    x-tooltip="{
        content: @js($tooltip),
        theme: $store.theme,
        allowHTML: @js($tooltip instanceof \Illuminate\Contracts\Support\Htmlable),
    }"
@endif
