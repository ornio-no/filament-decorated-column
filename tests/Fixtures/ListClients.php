<?php

namespace Ornio\FilamentDecoratedColumn\Tests\Fixtures;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Ornio\FilamentDecoratedColumn\BadgeDecoration;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\IconDecoration;

class ListClients extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Client::query())
            ->columns([
                DecoratedColumn::make('name')
                    ->prefixDecorations([
                        IconDecoration::make('type')->icon(Heroicon::OutlinedBriefcase),
                    ])
                    ->suffixBadges([
                        BadgeDecoration::make('tier')->label('Platinum Tier')->color('success'),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->table }}
            </div>
        BLADE;
    }
}
