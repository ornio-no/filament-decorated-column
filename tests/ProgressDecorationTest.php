<?php

use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\ProgressDecoration;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;

it('fills a progress bar to the value as a percentage of 100 by default', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([ProgressDecoration::make('usage')->value(42)]);

    expect($column->getSuffix()->toHtml())
        ->toContain('width: 42%')
        ->toContain('&mdash;');
});

it('fills a progress bar against a custom maximum, both read from the record', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([
            ProgressDecoration::make('usage')
                ->value(fn (Client $record): int => strlen($record->name))
                ->max(fn (Client $record): int => strlen($record->name) * 4),
        ]);

    expect($column->getPrefix()->toHtml())->toContain('width: 25%');
});

it('fills a progress bar completely above its maximum and leaves it empty below zero', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('over')->value(130)->max(120)])
        ->suffixDecorations([ProgressDecoration::make('under')->value(-15)]);

    expect($column->getPrefix()->toHtml())->toContain('width: 100%')
        ->and($column->getSuffix()->toHtml())->toContain('width: 0%');
});

it('fills a progress bar in the primary mark shade by default and in the given palette otherwise', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('usage')->value(42)])
        ->suffixDecorations([
            ProgressDecoration::make('usage')
                ->value(97)
                ->color(fn (Client $record): string => $record->name === 'Acme' ? 'danger' : 'success'),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('background-color: var(--primary-500)')
        ->and($column->getSuffix()->toHtml())
        ->toContain('background-color: var(--danger-500)')
        ->not->toContain('--primary-500');
});

it('draws the progress track in a neutral gray that follows dark mode', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('usage')->value(42)->color('success')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('background-color: light-dark(var(--gray-200), var(--gray-700))');
});

it('exposes a labelled progress bar to screen readers and hides an unlabelled one', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('seats')->value(30)->max(120)->label('Seats used')])
        ->suffixDecorations([ProgressDecoration::make('usage')->value(42)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('role="progressbar"')
        ->toContain('aria-valuenow="30"')
        ->toContain('aria-valuemin="0"')
        ->toContain('aria-valuemax="120"')
        ->toContain('aria-label="Seats used"')
        ->not->toContain('aria-hidden')
        ->and($column->getSuffix()->toHtml())
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="progressbar"')
        ->not->toContain('aria-valuenow');
});

it('tells screen readers the clamped value, not the raw one', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('over')->value(130)->max(120)->label('Seats used')])
        ->suffixDecorations([ProgressDecoration::make('under')->value(-15)->label('Balance')]);

    expect($column->getPrefix()->toHtml())->toContain('aria-valuenow="120"')
        ->and($column->getSuffix()->toHtml())->toContain('aria-valuenow="0"');
});

it('renders nothing, and no separator, for a null progress value', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffix('seats')
        ->prefixDecorations([ProgressDecoration::make('usage')->value(null)])
        ->suffixDecorations([ProgressDecoration::make('usage')->value(fn (Client $record): ?int => null)]);

    expect($column->getPrefix())->toBeNull()
        ->and($column->getSuffix())->toBe('seats');
});

it('gives a progress bar a tooltip', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('usage')->value(42)->tooltip(fn (Client $record): string => "{$record->name}: 42 of 100 seats")])
        ->suffixDecorations([ProgressDecoration::make('usage')->value(42)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('x-tooltip=')
        ->toContain("content: 'Acme: 42 of 100 seats'")
        ->toContain('theme: $store.theme')
        ->toContain('allowHTML: false')
        ->and($column->getSuffix()->toHtml())
        ->not->toContain('x-tooltip');
});

it('shows an empty progress bar when the maximum is zero or less', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ProgressDecoration::make('zero')->value(5)->max(0)])
        ->suffixDecorations([ProgressDecoration::make('negative')->value(5)->max(-10)]);

    expect($column->getPrefix()->toHtml())->toContain('width: 0%')
        ->and($column->getSuffix()->toHtml())->toContain('width: 0%');
});
