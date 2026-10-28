<?php

namespace Ornio\FilamentDecoratedColumn;

use Closure;

/**
 * A thin bar showing how far a value has come toward its maximum (100
 * unless max() is set). The fill is primary unless coloured. With a
 * label() it is announced as a progress bar; without one it is
 * decorative and hidden from screen readers.
 */
class ProgressDecoration extends Decoration
{
    protected string $view = 'filament-decorated-column::progress';

    protected int | float | Closure | null $value = null;

    protected int | float | Closure $max = 100;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor('primary');
    }

    public function value(int | float | Closure | null $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function max(int | float | Closure $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * A null value means nothing to show, so the slot skips the bar.
     */
    public function isHidden(): bool
    {
        return parent::isHidden() || $this->getValue() === null;
    }

    public function getValue(): int | float | null
    {
        return $this->evaluate($this->value);
    }

    public function getMax(): int | float
    {
        return $this->evaluate($this->max);
    }

    /**
     * The value clamped between 0 and the maximum. A maximum of zero or
     * less clamps it to 0.
     */
    public function getClampedValue(): int | float
    {
        return max(min($this->getValue(), $this->getMax()), 0);
    }

    /**
     * How full the bar is, from 0 to 100. A maximum of zero or less
     * leaves the bar empty.
     */
    public function getPercentage(): float
    {
        $max = $this->getMax();

        return $max > 0 ? $this->getClampedValue() / $max * 100 : 0;
    }
}
