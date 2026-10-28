@use('Filament\Support\Enums\IconSize')

@php
    $arrow = $getArrow();
    $textColor = $getTextColor();
@endphp

<span
    style="display: inline-flex; align-items: center; gap: 0.125rem; white-space: nowrap;@if ($textColor !== null) color: {{ $textColor }};@endif"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>
    {{-- The signed number carries the direction, so the arrow is hidden from screen readers. --}}
    @if ($arrow !== null)
        <span aria-hidden="true" style="display: inline-flex;">
            {{ \Filament\Support\generate_icon_html($arrow, size: IconSize::Small) }}
        </span>
    @endif
    <span>{{ $getFormattedValue() }}</span>
</span>
