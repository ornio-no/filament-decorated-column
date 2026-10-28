<?php

use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;
use Ornio\FilamentDecoratedColumn\TextDecoration;

it('shows its label from a closure that reads the record', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('owner')->label(fn (Client $record): string => "(owner of {$record->name})"),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('(owner of Acme)')
        ->toContain('&mdash;');
});

it('takes its label from its name when none is given', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TextDecoration::make('account_manager')]);

    expect($column->getPrefix()->toHtml())->toContain('Account manager');
});

it('escapes HTML in its label', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => '<script>alert(1)</script>']))
        ->suffixDecorations([TextDecoration::make('raw')->label(fn (Client $record): string => $record->name)]);

    expect($column->getSuffix()->toHtml())
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>');
});

it('is gray shade 500 by default and when gray is given', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TextDecoration::make('you')])
        ->suffixDecorations([TextDecoration::make('you')->color('gray')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('color: var(--gray-500)')
        ->not->toContain('light-dark(')
        ->and($column->getSuffix()->toHtml())
        ->toContain('color: var(--gray-500)')
        ->not->toContain('light-dark(');
});

it('uses shade 600 in light mode and 400 in dark mode for a palette colour', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TextDecoration::make('overdue')->color(fn (): string => 'danger')]);

    expect($column->getSuffix()->toHtml())
        ->toContain('color: light-dark(var(--danger-600), var(--danger-400))')
        ->not->toContain('--danger-500');
});

it('adds Filament font classes for weight and font family', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->label('#1042')->weight(FontWeight::Bold)->fontFamily(FontFamily::Mono),
            TextDecoration::make('you')->weight('semibold'),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('fi-font-bold')
        ->toContain('fi-font-mono')
        ->toContain('fi-font-semibold');
});

it('sets no font size, so it takes the size of the text around it', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TextDecoration::make('you')->weight(FontWeight::Bold)->fontFamily(FontFamily::Mono)]);

    expect($column->getSuffix()->toHtml())
        ->not->toContain('font-size')
        ->not->toContain('fi-size-');
});

it('shows a tooltip on hover', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->label('#1042')->tooltip(fn (Client $record): string => "Order for {$record->name}"),
            TextDecoration::make('you'),
        ]);

    $html = $column->getSuffix()->toHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)
        ->toContain("content: 'Order for Acme'")
        ->toContain('allowHTML: false')
        ->toContain('theme: $store.theme');
});
