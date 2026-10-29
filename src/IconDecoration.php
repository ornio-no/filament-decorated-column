<?php

namespace Ornio\FilamentDecoratedColumn;

use BackedEnum;
use Closure;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Htmlable;

/**
 * A bare icon beside the column text. With a label() it is announced
 * to screen readers under that name (use tooltip() to show it on hover
 * too); without one it is decorative and hidden from them — right when
 * the text beside it already says what the icon means. Colour defaults
 * to gray.
 */
class IconDecoration extends Decoration
{
    protected string $view = 'filament-decorated-column::icon';

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

    protected IconSize | string | Closure | null $size = null;

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function size(IconSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->icon);
    }

    public function getSize(): IconSize
    {
        $size = $this->evaluate($this->size) ?? IconSize::Small;

        return $size instanceof IconSize ? $size : (IconSize::tryFrom($size) ?? IconSize::Small);
    }
}
