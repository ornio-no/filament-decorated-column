@use('Filament\Support\Enums\FontFamily')
@use('Filament\Support\Enums\FontWeight')

@php
    $fontFamily = $getFontFamily();
    $weight = $getWeight();
    $textColor = $getTextColor();

    $classes = implode(' ', array_filter([
        $fontFamily instanceof FontFamily ? 'fi-font-' . $fontFamily->value : $fontFamily,
        $weight instanceof FontWeight ? 'fi-font-' . $weight->value : $weight,
    ]));
@endphp

<span
    @if (filled($classes))
        class="{{ $classes }}"
    @endif
    @if ($textColor !== null)
        style="color: {{ $textColor }};"
    @endif
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>{{ $getLabel() }}</span>
