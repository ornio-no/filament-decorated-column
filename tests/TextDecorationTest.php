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

it('sets a Filament weight as a numeric inline font-weight', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->label('#1042')->weight(FontWeight::Bold),
            TextDecoration::make('you')->weight('semibold'),
            TextDecoration::make('note')->weight(fn (): FontWeight => FontWeight::Thin),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('font-weight: 700;')
        ->toContain('font-weight: 600;')
        ->toContain('font-weight: 100;')
        ->not->toContain('fi-font-');
});

it('sets a Filament font family inline, through the panel font variables with generic fallbacks', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->label('#1042')->fontFamily(FontFamily::Mono),
            TextDecoration::make('quote')->fontFamily('serif'),
            TextDecoration::make('plain')->fontFamily(fn (): FontFamily => FontFamily::Sans),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('font-family: var(--mono-font-family, ui-monospace), ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;')
        ->toContain('font-family: var(--serif-font-family, ui-serif), ui-serif, Georgia, Cambria, serif;')
        ->toContain('font-family: var(--font-family, ui-sans-serif), ui-sans-serif, system-ui, sans-serif;')
        ->not->toContain('fi-font-');
});

it('keeps the colour beside the inline font settings', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->color('danger')->weight(FontWeight::Medium)->fontFamily(FontFamily::Mono),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toMatch('/style="color: light-dark\(var\(--danger-600\), var\(--danger-400\)\); font-weight: 500; font-family: var\(--mono-font-family/');
});

it('keeps a weight or font family that is not a Filament value as a class, for the theme to style', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TextDecoration::make('id')->weight('brand-weight')->fontFamily(fn (): string => 'brand-font'),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('class="brand-font brand-weight"')
        ->not->toContain('font-weight')
        ->not->toContain('font-family');
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

it('falls back to gray for a palette name that is not plain lowercase letters, digits and hyphens', function (string $palette) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TextDecoration::make('note')->color(fn (): string => $palette)]);

    expect($column->getSuffix()->toHtml())
        ->toContain('style="color: var(--gray-500);"')
        ->not->toContain('evil')
        ->not->toContain('light-dark(');
})->with([
    'a closing parenthesis and another declaration' => 'danger), var(--danger-400)); background-image: url(https://evil.test/x.png',
    'a semicolon' => 'danger;evil',
    'an uppercase letter' => 'Danger',
    'a trailing newline' => "danger\n",
]);
