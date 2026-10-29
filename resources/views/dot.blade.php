<span
    @include('filament-decorated-column::partials.screen-reader', ['decorative' => $isDecorative(), 'label' => $getLabel()])
    style="display: inline-block; flex-shrink: 0; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: {{ $getMarkColor() }};"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
></span>
