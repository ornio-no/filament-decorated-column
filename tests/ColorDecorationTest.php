<?php

use Ornio\FilamentDecoratedColumn\ColorDecoration;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;

it('shows a sample of the raw colour from a record closure', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => '#ff6600']))
        ->prefixDecorations([ColorDecoration::make('brand')->value(fn (Client $record): string => $record->name)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('background-color: #ff6600')
        ->toContain('&mdash;');
});

it('fills the sample with each accepted raw colour format', function (string $value) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([ColorDecoration::make('brand')->value($value)]);

    expect($column->getSuffix()->toHtml())->toContain("background-color: {$value};");
})->with([
    '#rgb' => '#f60',
    '#rgba' => '#f608',
    '#rrggbb' => '#ff6600',
    '#rrggbbaa' => '#ff660080',
    'upper-case hex' => '#FF6600',
    'rgb() with commas' => 'rgb(255, 102, 0)',
    'rgb() with spaces and a slash' => 'rgb(255 102 0 / 50%)',
    'rgba()' => 'rgba(255, 102, 0, .5)',
    'hsl() with an angle unit' => 'hsl(24deg 100% 50%)',
    'hsl() with a negative hue' => 'hsl(-336 100% 50%)',
    'hsla()' => 'hsla(0.07turn, 100%, 50%, 0.5)',
    'oklch()' => 'oklch(70% 0.19 48)',
    'oklch() with an alpha' => 'oklch(0.7 0.19 48 / 0.5)',
]);

it('draws the sample as a small rounded square with a thin neutral border', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ffffff')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('width: 1.25rem; height: 1.25rem;')
        ->toContain('border-radius: 0.25rem;')
        ->toContain('border: 1px solid rgb(128 128 128 / 0.4);');
});

it('ignores the inherited palette colour', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ff6600')->color('danger')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('background-color: #ff6600;')
        ->toContain('border: 1px solid rgb(128 128 128 / 0.4);')
        ->not->toContain('danger');
});

it('stays hidden when hidden() is set, even with an accepted raw colour', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ff6600')->hidden(fn (Client $record): bool => $record->name === 'Acme')]);

    expect($column->getPrefix())->toBeNull();
});

it('hides an unlabelled sample from screen readers', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ff6600')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"')
        ->not->toContain('aria-label');
});

it('announces a labelled sample as an image named by its label', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ff6600')->label(fn (Client $record): string => "{$record->name} orange")]);

    expect($column->getPrefix()->toHtml())
        ->toContain('role="img"')
        ->toContain('aria-label="Acme orange"')
        ->not->toContain('aria-hidden');
});

it('gives the sample a tooltip', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value('#ff6600')->tooltip('Brand orange')]);

    expect($column->getPrefix()->toHtml())
        ->toContain('x-tooltip=')
        ->toContain("content: 'Brand orange'")
        ->toContain('theme: $store.theme');
});

it('renders nothing for a null raw colour, leaving the plain affix with no separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value(null)])
        ->suffix(' kg')
        ->suffixDecorations([ColorDecoration::make('brand')->value(fn (): ?string => null)]);

    expect($column->getPrefix())->toBeNull()
        ->and($column->getSuffix())->toBe(' kg');
});

it('renders nothing for a value that is not an accepted raw colour', function (mixed $value) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ColorDecoration::make('brand')->value(fn (): mixed => $value)]);

    expect($column->getPrefix())->toBeNull();
})->with([
    'a plain colour word' => 'red',
    'a CSS keyword' => 'transparent',
    'a declaration break-out' => 'red; background-image: url(https://evil.example/track)',
    'a hex followed by another declaration' => '#ff6600; position: fixed; inset: 0',
    'a function followed by another declaration' => 'rgb(0 0 0); position: fixed',
    'another declaration followed by a hex' => 'x; background-image: url(https://evil.example/track); color: #fff',
    'an attribute break-out' => '#fff" onmouseover="alert(1)',
    'a url()' => 'url(https://evil.example/track)',
    'an expression()' => 'expression(alert(1))',
    'a CSS escape' => '\\23 ff6600',
    'a hex with a trailing newline' => "#ff6600\n",
    'a function with a trailing newline' => "rgb(0 0 0)\n",
    'a hex without the hash' => 'ff6600',
    'a hex with a non-hex digit' => '#ff660g',
    'a five-digit hex' => '#ff660',
    'a seven-digit hex' => '#ff66001',
    'a nine-digit hex' => '#ff6600ff0',
    'a function not on the list' => 'lab(50% 40 59)',
    'the color() function' => 'color(srgb 1 0.4 0)',
    'a keyword inside a function' => 'rgb(from red r g b)',
    'a nested var()' => 'rgb(var(--x) 0 0)',
    'a nested calc()' => 'rgb(calc(255) 0 0)',
    'a comment inside a function' => 'rgb(0 0 0 /* x */)',
    'an unclosed function' => 'rgb(255 0 0',
    'an empty function' => 'rgb()',
    'two functions' => 'rgb(0 0 0) rgb(255 255 255)',
    'non-ASCII digits' => 'rgb(２５５ 0 0)',
    'a number' => 0xFF6600,
]);
