<?php

use Illuminate\Support\Number;
use Ornio\FilamentDecoratedColumn\BadgeDecoration;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;
use Ornio\FilamentDecoratedColumn\TrendDecoration;

it('shows a rise as an up arrow, a plus sign and the success colour', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TrendDecoration::make('growth')->value(fn (Client $record): ?float => $record->name === 'Acme' ? 12.5 : null),
        ]);

    expect($column->getSuffix()->toHtml())
        ->toContain('>+12.5%<')
        ->toContain('color: light-dark(var(--success-600), var(--success-400))')
        ->toContain(trendArrow('up'))
        ->not->toContain(trendArrow('down'))
        ->toContain('&mdash;');
});

it('shows a fall as a down arrow, a minus sign and the danger colour', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('growth')->value(-3.2)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('>−3.2%<')
        ->toContain('color: light-dark(var(--danger-600), var(--danger-400))')
        ->toContain(trendArrow('down'))
        ->not->toContain(trendArrow('up'))
        ->toContain('&mdash;');
});

it('shows zero in gray with no arrow and no sign', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(0)]);

    expect($column->getSuffix()->toHtml())
        ->toContain('>0.0%<')
        ->toContain('color: var(--gray-500)')
        ->not->toContain('<svg');
});

it('treats a change that rounds to zero as zero, whichever way it leans', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('growth')->value(0.04)])
        ->suffixDecorations([TrendDecoration::make('growth')->value(fn (): float => -0.04)]);

    foreach ([$column->getPrefix()->toHtml(), $column->getSuffix()->toHtml()] as $html) {
        expect($html)
            ->toContain('>0.0%<')
            ->toContain('color: var(--gray-500)')
            ->not->toContain('<svg');
    }
});

it('swaps the success and danger colours with inverse(), keeping the arrow and sign', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('churn')->value(-2)->inverse()])
        ->suffixDecorations([
            TrendDecoration::make('cost')->value(4)->inverse(fn (): bool => true),
            TrendDecoration::make('revenue')->value(1)->inverse(false),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('>−2.0%<')
        ->toContain(trendArrow('down'))
        ->toContain('color: light-dark(var(--success-600), var(--success-400))')
        ->not->toContain('--danger-')
        ->and($column->getSuffix()->toHtml())
        ->toContain('>+4.0%<')
        ->toContain('color: light-dark(var(--danger-600), var(--danger-400))')
        ->toContain('>+1.0%<')
        ->toContain('color: light-dark(var(--success-600), var(--success-400))');
});

it('rounds the change to one decimal place by default and to decimals() places when given', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('growth')->value(12.3456)])
        ->suffixDecorations([
            TrendDecoration::make('whole')->value(7.6)->decimals(0),
            TrendDecoration::make('fine')->value(fn (): float => -0.125)->decimals(fn (): int => 2),
            TrendDecoration::make('padded')->value(3)->decimals(2),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('>+12.3%<')
        ->and($column->getSuffix()->toHtml())
        ->toContain('>+8%<')
        ->toContain('>−0.13%<')
        ->toContain('>+3.00%<');
});

it('decides the direction after rounding to decimals() places', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(0.4)->decimals(0)]);

    expect($column->getSuffix()->toHtml())
        ->toContain('>0%<')
        ->toContain('color: var(--gray-500)')
        ->not->toContain('<svg');
});

it('appends unit() exactly as given and no unit when it is null', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('points')->value(2)->unit(' pts')])
        ->suffixDecorations([
            TrendDecoration::make('revenue')->value(-150)->decimals(0)->unit(fn (): string => '€'),
            TrendDecoration::make('orders')->value(5)->decimals(0)->unit(null),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('>+2.0 pts<')
        ->and($column->getSuffix()->toHtml())
        ->toContain('>−150€<')
        ->toContain('>+5<');
});

it('escapes HTML in the unit', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(1)->unit('<b>x</b>')]);

    expect($column->getSuffix()->toHtml())
        ->toContain('+1.0&lt;b&gt;x&lt;/b&gt;')
        ->not->toContain('<b>');
});

it('leaves the slot empty when the change is null, so the owner shows its plain affix with no separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffix(' NOK')
        ->prefixDecorations([TrendDecoration::make('growth')->value(fn (): ?float => null)])
        ->suffixDecorations([TrendDecoration::make('growth')]);

    expect($column->getPrefix())->toBeNull()
        ->and($column->getSuffix())->toBe(' NOK');
});

it('leaves the slot empty for a change that is not a finite number', function (mixed $change) {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffix(' NOK')
        ->prefixDecorations([TrendDecoration::make('growth')->value(fn (): mixed => $change)])
        ->suffixDecorations([TrendDecoration::make('growth')->value(fn (): mixed => $change)]);

    expect($column->getPrefix())->toBeNull()
        ->and($column->getSuffix())->toBe(' NOK');
})->with([
    'NAN' => NAN,
    'INF' => INF,
    '-INF' => -INF,
    'a numeric string too large for a float' => '1e999',
]);

it('hides the arrow from screen readers and leaves the signed number to carry the direction', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(12.5)]);

    $html = $column->getSuffix()->toHtml();

    $xpath = htmlXPath($html);

    expect($xpath->query('//svg'))->toHaveCount(1)
        ->and($xpath->query('//svg[ancestor-or-self::*[@aria-hidden="true"]]'))->toHaveCount(1)
        ->and($xpath->query('//*[text()="+12.5%"]'))->toHaveCount(1)
        ->and($xpath->query('//*[text()="+12.5%"][ancestor-or-self::*[@aria-hidden="true"]]'))->toHaveCount(0)
        ->and($xpath->query('//*[@role or @aria-label]'))->toHaveCount(0)
        ->and($html)->not->toMatch('/<[^>]*aria-hidden=[^>]*aria-hidden=/');
});

it('shows a tooltip on hover', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            TrendDecoration::make('growth')->value(12.5)->tooltip(fn (Client $record): string => "{$record->name} vs last month"),
            TrendDecoration::make('margin')->value(1),
        ]);

    $html = $column->getSuffix()->toHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)
        ->toContain("content: 'Acme vs last month'")
        ->toContain('allowHTML: false')
        ->toContain('theme: $store.theme');
});

it('formats the number with a plain decimal point and no grouping, whatever the app locale', function () {
    app()->setLocale('de');

    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(-12345.678)->decimals(2)]);

    expect($column->getSuffix()->toHtml())->toContain('>−12345.68%<');
});

arch('formats numbers without the intl extension')
    ->expect(TrendDecoration::class)
    ->not->toUse([Number::class, NumberFormatter::class]);

it('reads a numeric string from a closure, as a decimal column gives, and treats any other string as no change', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->prefixDecorations([TrendDecoration::make('growth')->value(fn (): string => '12.50')])
        ->suffixDecorations([TrendDecoration::make('growth')->value(fn (): string => 'n/a')]);

    expect($column->getPrefix()->toHtml())->toContain('>+12.5%<')
        ->and($column->getSuffix())->toBeNull();
});

it('colours by direction even when color() is given', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('growth')->value(5)->color('primary')]);

    expect($column->getSuffix()->toHtml())
        ->toContain('color: light-dark(var(--success-600), var(--success-400))')
        ->not->toContain('--primary-');
});

it('sits beside a badge in the same slot, behind one separator', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([
            BadgeDecoration::make('vip')->label('VIP'),
            TrendDecoration::make('growth')->value(12.5),
        ]);

    $html = $column->getSuffix()->toHtml();

    expect($html)
        ->toContain('VIP')
        ->toContain('>+12.5%<')
        ->and(substr_count($html, '&mdash;'))->toBe(1);
});

it('stays gray at zero with inverse()', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Acme']))
        ->suffixDecorations([TrendDecoration::make('churn')->value(0)->inverse()]);

    expect($column->getSuffix()->toHtml())
        ->toContain('color: var(--gray-500)')
        ->not->toContain('<svg');
});
