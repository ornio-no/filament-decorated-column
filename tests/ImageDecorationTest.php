<?php

use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\ImageDecoration;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;

it('shows the image from a URL that reads the record', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([
            ImageDecoration::make('logo')->image(fn (Client $record): string => "https://example.com/{$record->name}.png"),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('<img')
        ->toContain('src="https://example.com/Acme.png"')
        ->toContain('&mdash;');
});

it('fills its box without stretching the picture and loads lazily', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([ImageDecoration::make('logo')->image('https://example.com/logo.png')]);

    expect($column->getSuffix()->toHtml())
        ->toContain('object-fit: cover;')
        ->toContain('loading="lazy"');
});

it('keeps the image square by default and rounds it fully with circular()', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ImageDecoration::make('logo')->image('https://example.com/logo.png')])
        ->suffixDecorations([
            ImageDecoration::make('avatar')->image('https://example.com/avatar.png')->circular(),
            ImageDecoration::make('flag')->image('https://example.com/no.png')->circular(fn (): bool => false),
        ]);

    expect($column->getPrefix()->toHtml())
        ->not->toContain('border-radius')
        ->and(substr_count($column->getSuffix()->toHtml(), 'border-radius: 9999px;'))->toBe(1);
});

it('sizes the image one line tall by default and to a CSS length or a number of pixels when given', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ImageDecoration::make('avatar')->image('https://example.com/avatar.png')])
        ->suffixDecorations([
            ImageDecoration::make('logo')->image('https://example.com/logo.png')->size('2em'),
            ImageDecoration::make('flag')->image('https://example.com/no.png')->size(fn (): int => 32),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('width: 1.25rem;')
        ->toContain('height: 1.25rem;')
        ->and($column->getSuffix()->toHtml())
        ->toContain('width: 2em;')
        ->toContain('height: 2em;')
        ->toContain('width: 32px;')
        ->toContain('height: 32px;');
});

it('accepts a size as a positive length in a CSS unit', function (string $size) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ImageDecoration::make('logo')->image('https://example.com/logo.png')->size(fn (): string => $size)]);

    expect($column->getPrefix()->toHtml())
        ->toContain("width: {$size};")
        ->toContain("height: {$size};");
})->with(['12px', '1.5rem', '2em', '50%', '.75rem']);

it('falls back to the default size for anything but a CSS length or a number of pixels', function (mixed $size) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ImageDecoration::make('logo')->image('https://example.com/logo.png')->size(fn (): mixed => $size)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('width: 1.25rem;')
        ->toContain('height: 1.25rem;')
        ->not->toContain('evil');
})->with([
    'another declaration after a length' => '2rem; background-image: url(https://evil.test/x.png)',
    'a function' => 'calc(1rem + 2px)',
    'a word' => 'large',
    'a number without a unit' => '32',
    'an unknown unit' => '2foo',
    'a negative length' => '-2rem',
    'a trailing newline' => "2rem\n",
    'a float' => 1.5,
]);

it('gives an unlabelled image empty alternative text and a labelled one its label', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([ImageDecoration::make('avatar')->image('https://example.com/avatar.png')])
        ->suffixDecorations([
            ImageDecoration::make('flag')->image('https://example.com/no.png')->label(fn (Client $record): string => "Flag of {$record->name}"),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('alt=""')
        ->not->toContain('Avatar')
        ->and($column->getSuffix()->toHtml())
        ->toContain('alt="Flag of Acme"');
});

it('gives an image a tooltip', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([
            ImageDecoration::make('avatar')->image('https://example.com/avatar.png')->tooltip(fn (Client $record): string => "Assigned to {$record->name}"),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('x-tooltip=')
        ->toContain("content: 'Assigned to Acme'")
        ->toContain('theme: $store.theme');
});

it('leaves the slot empty when the URL is null, so the owner shows its plain affix with no separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefix('Client: ')
        ->prefixDecorations([ImageDecoration::make('logo')->image(fn (): ?string => null)])
        ->suffixDecorations([ImageDecoration::make('flag')->image(null)]);

    expect($column->getPrefix())->toBe('Client: ')
        ->and($column->getSuffix())->toBeNull();
});
