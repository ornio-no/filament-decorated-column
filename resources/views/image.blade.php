@php
    $size = $getSize();
@endphp

{{-- An empty alt hides a decorative image from screen readers. --}}
<img
    src="{{ $getImage() }}"
    alt="{{ $isDecorative() ? '' : $getLabel() }}"
    loading="lazy"
    style="width: {{ $size }}; height: {{ $size }}; object-fit: cover;@if ($isCircular()) border-radius: 9999px;@endif"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>
