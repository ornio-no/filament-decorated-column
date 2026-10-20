@use('Filament\Schemas\View\Components\IconComponent')
@use('Filament\Support\View\ComponentAttributeBag')

@php
    $tooltip = $getTooltip();

    // Filament only tints icons that are direct children of a text cell,
    // so the colour is set here: gray-400 by default, shade 500 for any
    // other palette (the same shades a TextColumn icon uses).
    $color = $getColor() ?? 'gray';

    $iconAttributes = new ComponentAttributeBag(['aria-hidden' => 'true']);

    $iconAttributes = is_string($color)
        ? $iconAttributes->style('color: var(--' . e($color) . '-' . ($color === 'gray' ? 400 : 500) . ')')
        : $iconAttributes->color(IconComponent::class, $color);
@endphp

<span
    @if ($isDecorative())
        aria-hidden="true"
    @else
        role="img"
        aria-label="{{ $getLabel() }}"
    @endif
    style="display: inline-flex;"
    @if (filled($tooltip))
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
            allowHTML: @js($tooltip instanceof \Illuminate\Contracts\Support\Htmlable),
        }"
    @endif
>
    {{ \Filament\Support\generate_icon_html($getIcon(), attributes: $iconAttributes, size: $getSize()) }}
</span>
