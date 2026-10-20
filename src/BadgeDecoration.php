<?php

namespace Ornio\FilamentDecoratedColumn;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;

/**
 * A badge beside the column text: label, color, size, weight, font
 * family, pill, visibility, and an optional icon and tooltip.
 */
class BadgeDecoration extends Decoration
{
    use HasFontFamily;
    use HasWeight;

    protected string $view = 'filament-decorated-column::badge';

    protected bool | Closure | null $shouldBePill = null;

    protected Size | string | Closure | null $size = null;

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

    protected IconPosition | string | Closure | null $iconPosition = null;

    /**
     * Overrides the owning column's asPills() for this badge alone;
     * null follows the column.
     */
    public function isPill(bool | Closure | null $condition = true): static
    {
        $this->shouldBePill = $condition;

        return $this;
    }

    public function size(Size | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconPosition(IconPosition | string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getSize(): Size | string
    {
        $size = $this->evaluate($this->size) ?? Size::Small;

        if (! is_string($size)) {
            return $size;
        }

        return Size::tryFrom($size) ?? $size;
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->icon);
    }

    public function getIconPosition(): IconPosition
    {
        $position = $this->evaluate($this->iconPosition) ?? IconPosition::Before;

        return $position instanceof IconPosition ? $position : (IconPosition::tryFrom($position) ?? IconPosition::Before);
    }

    public function shouldBePill(): bool
    {
        $shouldBePill = $this->evaluate($this->shouldBePill);

        if ($shouldBePill !== null) {
            return (bool) $shouldBePill;
        }

        return method_exists($this->column, 'shouldBePills') && $this->column->shouldBePills();
    }
}
