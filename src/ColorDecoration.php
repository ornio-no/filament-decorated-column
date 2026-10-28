<?php

declare(strict_types=1);

namespace Ornio\FilamentDecoratedColumn;

use Closure;

/**
 * A small square showing a raw colour from the data, such as a product's
 * hex code, as a sample of itself. The inherited color() is ignored.
 *
 * The raw colour lands in an inline style, where HTML escaping does not
 * stop CSS injection, so only known colour formats are shown. Any other
 * value, null included, counts as nothing to show and hides the decoration.
 */
class ColorDecoration extends Decoration
{
    /**
     * #rgb, #rgba, #rrggbb or #rrggbbaa, or rgb(), rgba(), hsl(), hsla() or
     * oklch() holding only numbers (with an optional %, deg, grad, rad or
     * turn unit) separated by spaces, commas or slashes. \z, not $, so a
     * trailing newline is rejected too.
     */
    private const PATTERN = '/\A(?:'
        . '#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})'
        . '|(?:rgba?|hsla?|oklch)\( *' . self::NUMBER . '(?:(?: *[,\/] *| +)' . self::NUMBER . ')* *\)'
        . ')\z/i';

    private const NUMBER = '[+-]?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)(?:%|deg|grad|rad|turn)?';

    protected string $view = 'filament-decorated-column::color';

    protected string | Closure | null $value = null;

    public function value(string | Closure | null $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * The raw colour, or null when it is missing or not in an accepted
     * format.
     */
    public function getValue(): ?string
    {
        $value = $this->evaluate($this->value);

        if (! is_string($value) || preg_match(self::PATTERN, $value) !== 1) {
            return null;
        }

        return $value;
    }

    public function isHidden(): bool
    {
        return parent::isHidden() || $this->getValue() === null;
    }
}
