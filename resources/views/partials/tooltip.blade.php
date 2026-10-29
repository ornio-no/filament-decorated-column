{{-- The x-tooltip attribute for a decoration's root element. Include it inside the tag, passing ['tooltip' => $getTooltip()]. An Htmlable tooltip is turned into its HTML first: older Laravel 11 releases' @js() encode it as an empty object. --}}
@php
    $isHtml = $tooltip instanceof \Illuminate\Contracts\Support\Htmlable;
@endphp

@if (filled($tooltip))
    x-tooltip="{
        content: @js($isHtml ? $tooltip->toHtml() : $tooltip),
        theme: $store.theme,
        allowHTML: @js($isHtml),
    }"
@endif
