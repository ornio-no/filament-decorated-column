<?php

use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Livewire\Livewire;
use Ornio\FilamentDecoratedColumn\Badge;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\DecoratedEntry;
use Ornio\FilamentDecoratedColumn\Icon;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\Client;
use Ornio\FilamentDecoratedColumn\Tests\Fixtures\ListClients;

it('renders prefix and suffix badges', function () {
    $client = Client::create(['name' => 'Acme']);

    $column = DecoratedColumn::make('name')
        ->record($client)
        ->prefixBadges([
            Badge::make('vip')->label(fn (Client $record): string => "VIP {$record->name}")->color('success'),
        ])
        ->suffixBadges([
            Badge::make('hot')->color('danger'),
            Badge::make('hidden')->label('Never shown')->visible(false),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('VIP Acme')
        ->toContain('fi-badge')
        ->toContain('&mdash;')
        ->and($column->getSuffix()->toHtml())
        ->toContain('Hot')
        ->not->toContain('Never shown');
});

it('accepts badges from a closure, as pills when the column asks', function () {
    $client = Client::create(['name' => 'Globex']);

    $column = DecoratedColumn::make('name')
        ->record($client)
        ->asPills()
        ->suffixBadges(fn (): array => [Badge::make('a'), Badge::make('b')->isPill(false)]);

    expect(substr_count($column->getSuffix()->toHtml(), 'border-radius: 9999px'))->toBe(1);
});

it('renders icons inside badges and as bare decorations, with the separator on both', function () {
    $client = Client::create(['name' => 'Globex']);

    $column = DecoratedColumn::make('name')
        ->record($client)
        ->prefixDecorations([
            Icon::make('type')->icon(Heroicon::OutlinedBriefcase)->label('Business')->tooltip('Business client'),
        ])
        ->suffixBadges([
            Badge::make('paid')->icon(Heroicon::OutlinedCheckCircle)->iconPosition(IconPosition::After),
        ]);

    $prefix = $column->getPrefix()->toHtml();

    expect($prefix)
        ->toContain('aria-label="Business"')
        ->toContain('Business client')
        ->toContain('<svg')
        ->toContain('&mdash;')
        ->and($column->getSuffix()->toHtml())
        ->toContain('<svg')
        ->toContain('Paid')
        ->toContain('&mdash;');
});

it('uses a custom separator without splitting the column text on it', function () {
    $client = Client::create(['name' => 'Acme: Kosovo']);

    $column = DecoratedColumn::make('name')
        ->record($client)
        ->separator(':')
        ->suffixBadges([Badge::make('new')]);

    expect($column->getSeparator())->toBeNull()
        ->and($column->getSuffix()->toHtml())->toContain('<span style="opacity:0.375;">:</span>');
});

it('decorates infolist entries the same way', function () {
    $client = Client::create(['name' => 'Acme']);

    $entry = DecoratedEntry::make('name')
        ->suffixBadges([Badge::make('vip')->label(fn (Client $record): string => "VIP {$record->name}")]);

    $entry->container(Schema::make()->record($client));

    expect($entry->getSuffix()->toHtml())->toContain('VIP Acme');
});

it('hides an unlabelled icon from screen readers', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Globex']))
        ->prefixDecorations([Icon::make('type')->icon(Heroicon::OutlinedBriefcase)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"');
});

it('drops the separator when it is blank, widening the gap instead', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Globex']))
        ->separator('')
        ->prefixDecorations([Icon::make('type')->icon(Heroicon::OutlinedBriefcase)]);

    expect($column->getPrefix()->toHtml())
        ->toContain('margin-inline-end:0.5rem')
        ->not->toContain('opacity:0.375');
});

it('tints icons gray by default and with the given palette otherwise', function () {
    $column = DecoratedColumn::make('name')
        ->record(Client::create(['name' => 'Globex']))
        ->prefixDecorations([
            Icon::make('default')->icon(Heroicon::OutlinedBriefcase),
            Icon::make('paid')->icon(Heroicon::OutlinedCheckCircle)->color('success'),
        ]);

    expect($column->getPrefix()->toHtml())
        ->toContain('color: var(--gray-400)')
        ->toContain('color: var(--success-500)');
});

it('renders the decorations inside a real Filament table cell', function () {
    Client::create(['name' => 'Acme']);

    $html = Livewire::test(ListClients::class)->html();

    $document = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML($html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    // Only the decorated cell is inspected: the table around it can render
    // its own SVGs (pagination, actions), while a plain TextColumn cell
    // renders none, so an <svg> here can only come from the Icon.
    $cells = (new DOMXPath($document))
        ->query("//td[contains(concat(' ', normalize-space(@class), ' '), ' fi-ta-cell-name ')]");

    expect($cells)->toHaveCount(1);

    expect($document->saveHTML($cells->item(0)))
        ->toContain('Acme')
        ->toContain('Platinum Tier')
        ->toContain('<svg');
});
