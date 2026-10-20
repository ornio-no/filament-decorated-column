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
