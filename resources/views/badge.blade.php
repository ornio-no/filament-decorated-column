@use('Filament\Support\Enums\FontFamily')
@use('Filament\Support\Enums\FontWeight')
@use('Illuminate\Support\Arr')

@php
    $fontFamily = $getFontFamily();
    $weight = $getWeight() ?? FontWeight::Medium;
@endphp

<x-filament::badge
    :color="$getColor() ?? 'primary'"
    :size="$getSize()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    :tooltip="$getTooltip()"
    :class="Arr::toCssClasses([
        $fontFamily instanceof FontFamily ? 'fi-font-' . $fontFamily->value : $fontFamily,
        $weight instanceof FontWeight ? 'fi-font-' . $weight->value : $weight,
    ])"
    :style="$shouldBePill() ? 'border-radius: 9999px; padding-inline: 0.5rem;' : null"
>
    {{ $getLabel() }}
</x-filament::badge>
