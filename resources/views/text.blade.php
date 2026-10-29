@use('Filament\Support\Enums\FontFamily')
@use('Filament\Support\Enums\FontWeight')

@php
    $fontFamily = $getFontFamily();
    $weight = $getWeight();
    $textColor = $getTextColor();

    // Filament styles its fi-font-* classes only inside its own text items,
    // so its enum values are set inline. The families read the variables
    // a panel sets, as Filament's own rules do, and fall back to generic
    // families outside a panel. Any other string stays a class, for the
    // consumer's theme to style.
    $classes = implode(' ', array_filter([
        is_string($fontFamily) ? $fontFamily : null,
        is_string($weight) ? $weight : null,
    ]));

    $style = implode(' ', array_filter([
        $textColor !== null ? 'color: ' . $textColor . ';' : null,
        $weight instanceof FontWeight ? 'font-weight: ' . match ($weight) {
            FontWeight::Thin => 100,
            FontWeight::ExtraLight => 200,
            FontWeight::Light => 300,
            FontWeight::Normal => 400,
            FontWeight::Medium => 500,
            FontWeight::SemiBold => 600,
            FontWeight::Bold => 700,
            FontWeight::ExtraBold => 800,
            FontWeight::Black => 900,
        } . ';' : null,
        $fontFamily instanceof FontFamily ? 'font-family: ' . match ($fontFamily) {
            FontFamily::Sans => 'var(--font-family, ui-sans-serif), ui-sans-serif, system-ui, sans-serif',
            FontFamily::Serif => 'var(--serif-font-family, ui-serif), ui-serif, Georgia, Cambria, serif',
            FontFamily::Mono => 'var(--mono-font-family, ui-monospace), ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
        } . ';' : null,
    ]));
@endphp

<span
    @if (filled($classes))
        class="{{ $classes }}"
    @endif
    @if (filled($style))
        style="{{ $style }}"
    @endif
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>{{ $getLabel() }}</span>
