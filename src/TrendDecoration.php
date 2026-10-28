<?php

namespace Ornio\FilamentDecoratedColumn;

use Closure;
use Filament\Support\Icons\Heroicon;

/**
 * A change beside the column text, such as "+12.5%" beside a revenue
 * figure: an arrow and a signed number, coloured by direction. The
 * colour follows the change, so the inherited color() is ignored.
 */
class TrendDecoration extends Decoration
{
    protected string $view = 'filament-decorated-column::trend';

    protected int | float | Closure | null $value = null;

    protected int | Closure $decimals = 1;

    protected string | Closure | null $unit = '%';

    protected bool | Closure $isInverse = false;

    public function value(int | float | Closure | null $change): static
    {
        $this->value = $change;

        return $this;
    }

    /**
     * How many decimal places the change is rounded to, 1 by default.
     */
    public function decimals(int | Closure $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getDecimals(): int
    {
        return (int) $this->evaluate($this->decimals);
    }

    /**
     * Appended to the number exactly as given, with no space added: '%'
     * by default, null for none.
     */
    public function unit(string | Closure | null $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->evaluate($this->unit);
    }

    /**
     * Swaps the success and danger colours, for a metric where down is
     * good, such as churn or cost. The arrow and sign still follow the
     * change.
     */
    public function inverse(bool | Closure $condition = true): static
    {
        $this->isInverse = $condition;

        return $this;
    }

    public function isInverse(): bool
    {
        return (bool) $this->evaluate($this->isInverse);
    }

    /**
     * The change rounded to decimals() places, or null when there is none.
     * A numeric string, which a decimal column gives, counts as a number.
     */
    public function getValue(): ?float
    {
        $change = $this->evaluate($this->value);

        if (! is_numeric($change)) {
            return null;
        }

        return round((float) $change, $this->getDecimals());
    }

    public function getColor(): string | array | null
    {
        $direction = $this->getDirection();

        return match ($this->isInverse() ? -$direction : $direction) {
            1 => 'success',
            -1 => 'danger',
            0 => 'gray',
        };
    }

    public function getArrow(): ?Heroicon
    {
        return match ($this->getDirection()) {
            1 => Heroicon::ArrowUp,
            -1 => Heroicon::ArrowDown,
            0 => null,
        };
    }

    public function getFormattedValue(): string
    {
        $sign = match ($this->getDirection()) {
            1 => '+',
            -1 => '−',
            0 => '',
        };

        return $sign . number_format(abs($this->getValue()), $this->getDecimals(), '.', '') . $this->getUnit();
    }

    /**
     * Without a change there is nothing to show, so the trend counts as
     * hidden and the slot skips it.
     */
    public function isHidden(): bool
    {
        return parent::isHidden() || $this->getValue() === null;
    }

    /**
     * 1 for a rise, -1 for a fall and 0 for no change, decided after
     * rounding so that a change shown as zero never counts as a rise.
     *
     * @return -1|0|1
     */
    protected function getDirection(): int
    {
        return $this->getValue() <=> 0;
    }
}
