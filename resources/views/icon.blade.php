@use('Filament\Schemas\View\Components\IconComponent')
@use('Filament\Support\View\ComponentAttributeBag')

@php
    // Filament only tints icons that are direct children of a text cell,
    // so the colour is set here.
    $markColor = $getMarkColor();

    $iconAttributes = new ComponentAttributeBag(['aria-hidden' => 'true']);

    $iconAttributes = $markColor !== null
        ? $iconAttributes->style('color: ' . e($markColor))
        : $iconAttributes->color(IconComponent::class, $getColor());
@endphp

<span
    @if ($isDecorative())
        aria-hidden="true"
    @else
        role="img"
        aria-label="{{ $getLabel() }}"
    @endif
    style="display: inline-flex;"
    @include('filament-decorated-column::partials.tooltip', ['tooltip' => $getTooltip()])
>
    {{ \Filament\Support\generate_icon_html($getIcon(), attributes: $iconAttributes, size: $getSize()) }}
</span>
