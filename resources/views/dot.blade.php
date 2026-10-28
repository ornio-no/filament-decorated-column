<span
    @if ($isDecorative())
        aria-hidden="true"
    @else
        role="img"
        aria-label="{{ $getLabel() }}"
    @endif
    style="display: inline-block; flex-shrink: 0; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: {{ $getMarkColor() }};"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
></span>
