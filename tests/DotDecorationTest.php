<?php

use Ornio\FilamentDecoratedColumn\BadgeDecoration;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\DotDecoration;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;

it('renders a dot with no colour as a gray mark', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([DotDecoration::make('status')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('background-color: var(--gray-400)')
        ->toContain('border-radius: 9999px')
        ->toContain('&mdash;');
});

it('renders a dot in the mark shade of a palette colour chosen from the record', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            DotDecoration::make('status')->color(fn (Client $record): string => $record->name === 'Acme' ? 'success' : 'danger'),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('background-color: var(--success-500)')
        ->not->toContain('--gray-400')
        ->toContain('&mdash;');
});

it('hides an unlabelled dot from screen readers', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([DotDecoration::make('status')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"')
        ->not->toContain('aria-label');
});

it('announces a labelled dot as an image named by its label', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([DotDecoration::make('status')->label(fn (Client $record): string => "{$record->name} is active")]);

    expect($column->getPrefix()->toHtml())
        ->toContain('role="img"')
        ->toContain('aria-label="Acme is active"')
        ->not->toContain('aria-hidden');
});

it('gives a dot a tooltip only when one is set', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([
            DotDecoration::make('status')->color('success')->tooltip(fn (Client $record): string => "{$record->name} paid on time"),
            DotDecoration::make('plain'),
        ]);

    $html = $column->getPrefix()->toHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)
        ->toContain("content: 'Acme paid on time'")
        ->toContain('allowHTML: false')
        ->toContain('theme: $store.theme');
});

it('renders nothing for a hidden dot, leaving the slot without a separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([DotDecoration::make('status')->hidden(fn (Client $record): bool => $record->name === 'Acme')])
        ->suffixDecorations([DotDecoration::make('status')->visible(false)]);

    expect($column->getPrefix())->toBeNull()
        ->and($column->getSuffix())->toBeNull();
});

it('renders a dot and a badge in one slot, in order, with the separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([
            DotDecoration::make('status')->color('success'),
            BadgeDecoration::make('vip')->label('VIP'),
        ]);

    $html = $column->getPrefix()->toHtml();

    expect($html)
        ->toContain('background-color: var(--success-500)')
        ->toContain('fi-badge')
        ->toContain('VIP')
        ->toContain('&mdash;')
        ->and(strpos($html, 'var(--success-500)'))->toBeLessThan(strpos($html, 'fi-badge'))
        ->and(strpos($html, 'fi-badge'))->toBeLessThan(strpos($html, '&mdash;'));
});
