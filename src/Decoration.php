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
 * One piece of a DecoratedColumn / DecoratedEntry prefix or suffix: a
 * BadgeDecoration or an IconDecoration. Closures resolve `$record` (the
 * row), `$state` (the decoration's label), and `$livewire` from the
 * owning column or entry.
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
        $color = $this->getColor() ?? 'gray';

        if (! is_string($color)) {
            return null;
        }

        return 'var(--' . $color . '-' . ($color === 'gray' ? 400 : 500) . ')';
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
