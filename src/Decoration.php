<?php

namespace Ornio\FilamentDecoratedColumn;

use Closure;
use Filament\Infolists\Components\Entry;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasColor;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\CanBeHidden;
use Filament\Tables\Columns\Concerns\HasLabel;
use Filament\Tables\Columns\Concerns\HasName;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

/**
 * One piece of a DecoratedColumn / DecoratedEntry prefix or suffix, such
 * as a badge, an icon or a trend. Each kind is a subclass with its own
 * view. Closures resolve `$record` (the row), `$state` (the decoration's
 * label), and `$livewire` from the owning column or entry.
 */
abstract class Decoration extends ViewComponent
{
    use CanBeHidden;
    use HasColor;
    use HasLabel;
    use HasName;

    protected Column | Entry $column;

    protected string | Htmlable | Closure | null $tooltip = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function column(Column | Entry $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function tooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(): string | Htmlable | null
    {
        return $this->evaluate($this->tooltip);
    }

    /**
     * True when no label() was given: screen readers skip the decoration
     * because the text beside it already says what it means. Checks the
     * raw label, since getLabel() falls back to the name.
     */
    public function isDecorative(): bool
    {
        return $this->label === null;
    }

    /**
     * The inline CSS colour of a mark such as an icon: gray shade 400 when
     * no colour is set, shade 500 of any other palette colour. Null for a
     * custom (array) palette, which has no named shades.
     */
    public function getMarkColor(): ?string
    {
        $color = $this->getPaletteName();

        if ($color === null) {
            return null;
        }

        return 'var(--' . $color . '-' . ($color === 'gray' ? 400 : 500) . ')';
    }

    /**
     * The inline CSS colour of text such as a text or trend decoration:
     * gray shade 500 in both modes when no colour is set or gray is given;
     * any other palette colour at shade 600 in light mode and 400 in dark
     * mode, through light-dark(), which follows the color-scheme Filament
     * sets in dark mode. Null for a custom (array) palette, which has no
     * named shades.
     */
    public function getTextColor(): ?string
    {
        $color = $this->getPaletteName();

        if ($color === null) {
            return null;
        }

        if ($color === 'gray') {
            return 'var(--gray-500)';
        }

        return 'light-dark(var(--' . $color . '-600), var(--' . $color . '-400))';
    }

    /**
     * The palette name that getMarkColor() and getTextColor() put into a
     * var() in an inline style: gray when no colour is set, and also when
     * the name holds anything but lowercase letters, digits and hyphens,
     * because a name from a closure could otherwise inject CSS. Null for
     * a custom (array) palette.
     */
    private function getPaletteName(): ?string
    {
        $color = $this->getColor() ?? 'gray';

        if (! is_string($color)) {
            return null;
        }

        return preg_match('/^[a-z0-9-]+\z/', $color) === 1 ? $color : 'gray';
    }

    public function getRecord(): ?Model
    {
        $record = $this->column->getRecord();

        return $record instanceof Model ? $record : null;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
            'state' => [$this->getLabel()],
            'livewire' => [$this->column->getLivewire()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
