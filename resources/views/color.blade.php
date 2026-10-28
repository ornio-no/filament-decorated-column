{{-- $getValue() only returns a raw colour in an accepted format, so it is safe in the style. The translucent gray border keeps white samples visible in light mode and black ones in dark mode. --}}
<span
    @include('filament-decorated-column::partials.screen-reader', ['decorative' => $isDecorative(), 'label' => $getLabel()])
    style="display: inline-block; width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; border: 1px solid rgb(128 128 128 / 0.4); background-color: {{ $getValue() }};"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
></span>
