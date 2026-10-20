# Filament Decorated Column

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ornio/filament-decorated-column.svg?style=flat-square)](https://packagist.org/packages/ornio/filament-decorated-column)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ornio-no/filament-decorated-column/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ornio-no/filament-decorated-column/actions?query=workflow%3Atests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ornio-no/filament-decorated-column/fix-code-style.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ornio-no/filament-decorated-column/actions?query=workflow%3Afix-code-style+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ornio/filament-decorated-column.svg?style=flat-square)](https://packagist.org/packages/ornio/filament-decorated-column)

`DecoratedColumn` and `DecoratedEntry` are Filament's `TextColumn` and `TextEntry` with a slot before and a slot after the text. The slots hold `Badge` and `Icon` decorations. A badge can carry an icon and a tooltip, an icon can stand on its own, and every decoration takes closures that see the row's record. The views use inline styles and Filament's own `fi-*` classes, so there is no theme to build and no `@source` line to add.

```php
use App\Models\Client;
use Filament\Support\Icons\Heroicon;
use Ornio\FilamentDecoratedColumn\Badge;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\Icon;

DecoratedColumn::make('name')
    ->prefixDecorations([
        Icon::make('type')->icon(Heroicon::OutlinedBriefcase)->label('Business'),
    ])
    ->suffixBadges([
        Badge::make('vip')
            ->label('VIP')
            ->color('success')
            ->visible(fn (Client $record): bool => $record->is_vip),
    ]);
```

Each row shows the briefcase, an em dash, and the name. VIP rows add another em dash and a green badge.

## Installation

```bash
composer require ornio/filament-decorated-column
```

Requires PHP 8.2 or later and Filament 5.7 or later. Laravel discovers the service provider. There is no panel plugin to register, no config to publish, and no theme to build.

To change the markup, publish the views:

```bash
php artisan vendor:publish --tag="filament-decorated-column-views"
```

## Usage

### Table columns

`DecoratedColumn` extends `TextColumn`, so `description()`, `searchable()`, `sortable()`, `wrap()`, and the rest work unchanged. Add decorations with `prefixDecorations()` and `suffixDecorations()`, or their aliases `prefixBadges()` and `suffixBadges()`. All four take an array of `Badge` and `Icon` objects, or a closure that returns one.

```php
use App\Models\Client;
use Filament\Tables\Table;
use Ornio\FilamentDecoratedColumn\Badge;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;

public static function configure(Table $table): Table
{
    return $table->columns([
        DecoratedColumn::make('name')
            ->description(fn (Client $record): ?string => $record->registration_number)
            ->suffixBadges([
                Badge::make('type')
                    ->label(fn (Client $record): string => $record->type->getLabel())
                    ->color(fn (Client $record): string => $record->type->getColor()),
            ])
            ->searchable()
            ->sortable(),
    ]);
}
```

Here `type` casts to an enum that implements Filament's `HasLabel` and `HasColor` contracts.

A bare icon in the prefix slot, driven by an enum that implements `HasIcon`:

```php
use App\Models\Activity;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Ornio\FilamentDecoratedColumn\DecoratedColumn;
use Ornio\FilamentDecoratedColumn\Icon;

DecoratedColumn::make('description')
    ->prefixDecorations([
        Icon::make('type')
            ->icon(fn (Activity $record): Heroicon => $record->type->getIcon())
            ->size(IconSize::Large),
    ])
    ->wrap(),
```

### Infolist entries

`DecoratedEntry` extends `TextEntry` and takes the same methods.

```php
use App\Models\Client;
use Filament\Schemas\Schema;
use Ornio\FilamentDecoratedColumn\Badge;
use Ornio\FilamentDecoratedColumn\DecoratedEntry;

public static function configure(Schema $schema): Schema
{
    return $schema->components([
        DecoratedEntry::make('name')
            ->suffixBadges([
                Badge::make('type')
                    ->label(fn (Client $record): string => $record->type->getLabel())
                    ->color(fn (Client $record): string => $record->type->getColor()),
            ]),
    ]);
}
```

Both work in any table or infolist, inside a panel or in the standalone packages.

## Badges

`Badge::make('name')` creates a badge. The name doubles as the default label: `Badge::make('hot')` reads "Hot" and `Badge::make('is_paid')` reads "Is paid".

| Method | What it sets | Default |
| --- | --- | --- |
| `label()` | The text. | Derived from the name. |
| `color()` | A Filament colour name such as `success` or `gray`, or a palette array. | `primary` |
| `size()` | `Size::ExtraSmall` to `Size::ExtraLarge`, or `xs` to `xl`. | `Size::Small` |
| `weight()` | A `FontWeight` case or its string value. | `FontWeight::Medium` |
| `fontFamily()` | `FontFamily::Sans`, `Serif`, or `Mono`, or the string. | Inherited. |
| `isPill()` | Full rounding (`border-radius: 9999px`) for this badge alone. `isPill(false)` opts out of the column's `asPills()`. `null` follows the column. | `null` |
| `icon()` | An icon, such as `Heroicon::OutlinedCheckCircle`. | None |
| `iconPosition()` | `IconPosition::Before` or `IconPosition::After`. | `IconPosition::Before` |
| `tooltip()` | Text shown on hover. | None |
| `visible()`, `hidden()` | Whether the badge renders. | Visible |

Every method accepts a closure. See [Closure parameters](#closure-parameters).

`weight()` and `fontFamily()` put Filament's `fi-font-*` class on the badge (`fi-font-bold`, `fi-font-mono`). Filament's stylesheet defines those classes for text items and links, not for badges, so they change a badge only where your theme styles `.fi-badge.fi-font-*`.

A hidden badge renders nothing. When every decoration in a slot is hidden, the slot's separator goes with it.

```php
use App\Models\TeamMember;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

Badge::make('you')
    ->color('gray')
    ->tooltip('You cannot change your own role.')
    ->visible(fn (TeamMember $record): bool => $record->user_id === auth()->id()),

Badge::make('paid')
    ->color('success')
    ->icon(Heroicon::OutlinedCheckCircle)
    ->iconPosition(IconPosition::After),
```

## Icons

`Icon::make('name')` renders an icon with no badge around it.

| Method | What it sets | Default |
| --- | --- | --- |
| `icon()` | The icon to draw. | None |
| `size()` | An `IconSize` case or its string value. | `IconSize::Small` |
| `color()` | A Filament colour name, or a palette array. Gray draws with `var(--gray-400)`; any other name uses shade 500, as a `TextColumn` icon does. | `gray` |
| `label()` | A name for screen readers. The icon gets `role="img"` and `aria-label`. | None |
| `tooltip()` | Text shown on hover. | None |
| `visible()`, `hidden()` | Whether the icon renders. | Visible |

Without `label()` the icon is decorative and carries `aria-hidden="true"`. That is right when the text beside it already says what the icon means. Give a label when the icon carries meaning on its own, and add `tooltip()` to show that meaning on hover too.

```php
Icon::make('type')
    ->icon(Heroicon::OutlinedBriefcase)
    ->label('Business')
    ->tooltip('Business client'),
```

## Pills

`asPills()` on the column or entry rounds every badge in both slots. It accepts a closure. A single badge overrides it with `isPill()` or `isPill(false)`.

```php
DecoratedColumn::make('name')
    ->asPills()
    ->suffixBadges([
        Badge::make('new'),
        Badge::make('draft')->isPill(false),
    ]);
```

## Separator

`separator()` sets the mark between the text and a non-empty slot. When you never call it, the mark is an em dash (`&mdash;`) at reduced opacity. Calling it with no argument sets a comma, because the method's own default is `','`. Pass a string or a closure. A blank string removes the mark and widens the gap between slot and text from 0.25rem to 0.5rem.

```php
DecoratedColumn::make('name')->separator('')->suffixBadges([/* ... */]);
DecoratedColumn::make('name')->separator('&middot;')->suffixBadges([/* ... */]);
```

The separator is output as raw HTML, so entities work and user input must never go there.

Text set with `prefix()` or `suffix()` still renders, between the separator and the column text.

On `TextColumn` and `TextEntry`, `separator()` means something else: it splits a string state into a list on that character. `DecoratedColumn` and `DecoratedEntry` replace that method, and the inherited `getSeparator()` stays null, so the state is never split. A value containing the separator character stays one value. If you need a list, give the column an array state.

## Closure parameters

Every closure on a `Badge` or `Icon` can ask for these parameters by name:

- `$record`: the row's Eloquent model, or null when the column has none.
- `$state`: the decoration's own resolved label, not the column's state.
- `$livewire`: the Livewire component that owns the table or infolist.

Parameters match by name, so call them exactly that. The type hint can be your model class: `fn (Client $record)`. Other type-hinted parameters resolve from the container, as in any Filament closure.

`$state` is for `color()`, `icon()`, `tooltip()`, `visible()`, and the like. Don't request it inside `label()`: the label is what `$state` resolves to, so the closure would call itself. Inside `weight()` and `fontFamily()`, Filament's own traits inject `$state` as null.

```php
use App\Models\Invoice;

Badge::make('status')
    ->label(fn (Invoice $record): string => $record->status->getLabel())
    ->color(fn (string $state): string => $state === 'Overdue' ? 'danger' : 'gray'),
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Flamur Mavraj](https://github.com/oxodesign)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
