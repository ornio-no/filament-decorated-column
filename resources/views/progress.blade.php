{{-- The track uses the grays of Filament's toggle. The first background is the fallback for browsers without light-dark(). --}}
<span
    @if ($isDecorative())
        aria-hidden="true"
    @else
        role="progressbar"
        aria-valuenow="{{ $getClampedValue() }}"
        aria-valuemin="0"
        aria-valuemax="{{ $getMax() }}"
        aria-label="{{ $getLabel() }}"
    @endif
    style="display: inline-block; flex-shrink: 0; width: 3.75rem; height: 0.375rem; overflow: hidden; border-radius: 9999px; background-color: var(--gray-200); background-color: light-dark(var(--gray-200), var(--gray-700));"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>
    <span style="display: block; height: 100%; width: {{ $getPercentage() }}%; background-color: {{ $getMarkColor() }};"></span>
</span>
