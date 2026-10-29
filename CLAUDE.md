# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`ornio/filament-decorated-column` is a Filament v5 standalone component package: `DecoratedColumn` (extends `TextColumn`) and `DecoratedEntry` (extends `TextEntry`) render decorations (`BadgeDecoration`, `IconDecoration`, `DotDecoration`, `ImageDecoration`, `TextDecoration`, `ColorDecoration`, `TrendDecoration`, `ProgressDecoration`) before or after their text. It started as a port of an app-local `Decorated` component, with the namespace, the view names, and the decoration class names changed (the original's `Badge` and `Icon`), and `getPrefix()`/`getSuffix()` widened to Filament 5.10's signature (see Architecture). There is no Plugin class, config, migration, translation, or JS/CSS build. `FilamentDecoratedColumnServiceProvider` (spatie `PackageServiceProvider`, auto-discovered) registers only the package name and the `filament-decorated-column::` view namespace. Filament's plugin docs say "The Plugin object is only used for Panel Providers. Standalone Plugins do not use this object.", so consumers have no `->plugin()` step: the column and entry work in any table or infolist, in or out of a panel.

## Commands

```bash
composer install

composer test                                  # Pest (vendor/bin/pest)
vendor/bin/pest tests/DecoratedColumnTest.php  # one file
vendor/bin/pest --filter="custom separator"    # one test by name
composer lint                                  # Pint, fixes in place
composer test:lint                             # Pint, check only
composer analyse                               # PHPStan level 4 on src/
composer refactor                              # Rector on src/
composer test:refactor                         # Rector, dry run
```

There is no npm step. The package ships no JS or CSS.

CI runs Pest across PHP 8.2 to 8.4 and Laravel 11 to 13 (Laravel 13 needs PHP 8.3 or later), under both `prefer-lowest` and `prefer-stable`, on Linux and Windows. Keep code compatible with that whole range. Composer 2.11 refuses packages with security advisories by default, which covers every Laravel 11 release, so the tests and PHPStan workflows add `laravel/framework` to `policy.advisories.ignore` for their Laravel 11 jobs only. Don't widen that to `policy.advisories.block: false`: other packages' advisories are what keep `prefer-lowest` off versions too old to run, such as Carbon 3.0.0 on PHP 8.4. The `filament/filament` floor in `composer.json` was found by running the suite under `prefer-lowest`; re-check it whenever the code starts using a newer Filament API. A CI workflow runs Pint on every push and auto-commits a "Fix styling" commit.

## Architecture

- `Decoration` (abstract, extends Filament's `ViewComponent`) is one item of a prefix or suffix slot. It has a name, a label (derived from the name when unset, through Filament's tables `HasLabel`), a colour, a tooltip, and `visible()`/`hidden()`. `HasDecorations` hands it the owning column or entry through `column()` before rendering, and `resolveDefaultClosureDependencyForEvaluationByName()` resolves `$record` (the owner's record), `$state` (the decoration's own label, so a `$state` parameter inside `label()` recurses), and `$livewire` (the owner's Livewire component) from that owner. Every setter's closure, data setters included, resolves its parameters there; the owner's state is never passed in.
- `Decoration` also holds what the views share. `isDecorative()` is true when no `label()` was given (it checks the raw `$label`, because `getLabel()` falls back to the name). The Icon, Dot, and Color views then render `aria-hidden="true"` instead of `role="img"` plus `aria-label`, through `partials/screen-reader.blade.php`; Progress renders it instead of its `role="progressbar"` attributes, and Image renders `alt=""`. `getMarkColor()` is a mark's inline colour: `var(--gray-400)` by default, shade 500 for any other palette. `getTextColor()` is text's: `var(--gray-500)` by default or for gray, otherwise `light-dark()` of shades 600 and 400, which follows the `color-scheme: dark` that Filament's panel `base.css` sets. Both return null for an array palette, which has no named shades. Both read the name through the private `getPaletteName()`, which falls back to gray for a name that doesn't match `^[a-z0-9-]+\z`, so a name returned by a closure can't inject CSS into the `var()`.
- Nothing to show counts as hidden. `ImageDecoration`, `ColorDecoration`, `TrendDecoration`, and `ProgressDecoration` override `isHidden()` as `parent::isHidden() || <no data>`, so `renderDecorations()` skips them through the same check as `hidden()`, and a slot holding only such decorations is empty and falls through to the parent with no separator. A new decoration with a data setter should do the same.
- `BadgeDecoration` adds size (default `Size::Small`), weight (default `FontWeight::Medium`, applied in the view), font family, icon, icon position (default before), and `isPill()`. `shouldBePill()` returns the badge's own value when it is not null and otherwise the owner's `shouldBePills()`.
- `IconDecoration` adds icon and size (default `IconSize::Small`). Its colour is set inline through `getMarkColor()` because Filament only tints icons that are direct children of a text cell; an array palette goes through `ComponentAttributeBag::color()` instead.
- `DotDecoration` adds nothing: a fixed 0.5rem circle in `getMarkColor()`.
- `TextDecoration` adds weight and font family (no defaults) and renders its escaped label in `getTextColor()`. Filament styles `fi-font-*` only inside its own text items, so the view sets a `FontWeight` as a numeric inline `font-weight` and a `FontFamily` as an inline `font-family` that reads the panel's `--font-family`, `--serif-font-family`, or `--mono-font-family` (what Filament's compiled `fi-font-*` rules read; its `--font-sans`/`--font-serif` theme variables are inlined at build time and not emitted) with generic fallbacks. Any other string stays a class.
- `ImageDecoration` adds `image()` (a URL), `size()` (an int of pixels or a string matching `LENGTH_PATTERN`, a non-negative number with a length or `%` unit, for width and height; anything else falls back to `1.25rem`, since a closure can return row data), and `circular()`. It is hidden when the URL is `blank()`, so an empty string counts as nothing to show.
- `ColorDecoration` adds `value()`, a raw colour. `getValue()` returns it only when it matches `PATTERN`: hex in 3, 4, 6, or 8 digits, or `rgb()`, `rgba()`, `hsl()`, `hsla()`, `oklch()` holding only numbers with optional `%`/`deg`/`grad`/`rad`/`turn` units, separated by commas, slashes, or literal spaces (tabs and newlines fail, and `\z` rejects a trailing newline). Anything else returns null, which hides it. The inherited `color()` is ignored.
- `TrendDecoration` adds `value()`, `decimals()` (default 1), `unit()` (default `'%'`, appended with no space), and `inverse()`. `getValue()` accepts anything `is_numeric()` and finite, so a numeric string from a decimal column counts and `NAN` or infinity counts as none, and rounds it; the direction is the rounded value `<=> 0`. It overrides `getColor()` to follow the direction (success, danger, or gray, with `inverse()` swapping the first two), so `color()` is ignored. Colour, arrow, and sign per direction come from the private `DIRECTIONS` lookup. The arrow is `Heroicon::ArrowUp`/`ArrowDown`, hidden from screen readers. The number goes through `number_format()` with the minus sign `−` (U+2212), because the package doesn't require `intl`; don't use `Illuminate\Support\Number`.
- `ProgressDecoration` adds `value()` and `max()` (default 100) and sets `defaultColor('primary')` in `setUp()`. `getClampedValue()` clamps the value to between 0 and the maximum and feeds `aria-valuenow`; `getPercentage()` is 0 when the maximum is zero or less, and the view reports such a maximum as `aria-valuemax="0"` so it never falls below `aria-valuemin`. The track is 3.75rem by 0.375rem, gray 200, or gray 700 in dark mode through `light-dark()`.
- `Concerns\HasDecorations` (used by `DecoratedColumn` and `DecoratedEntry`) owns the slots: `prefixDecorations()`/`suffixDecorations()` with `prefixBadges()`/`suffixBadges()` as aliases, `asPills()`, and `separator()`. It overrides `getPrefix()`/`getSuffix()`. `renderDecorations()` renders the slot's visible decorations and strips Livewire's block comments. A non-empty slot is wrapped in an inline-flex `<span>`, followed by the separator mark and the parent's affix, and returned as an `HtmlString`. An empty slot falls through to the parent. The signatures take `(mixed $state = null, ?Model $relatedRecord = null)` and forward `...func_get_args()`, because Filament 5.10 added those parameters to the parent while earlier 5.x parents take none; the docblock on `getPrefix()` explains the forwarding.
- `DecoratedColumn::setUp()` pins `formatStateUsing()` to identity. Filament's tables `CanFormatState::hasStateFormatting()` checks the raw `$prefix`/`$suffix` properties, which decorations never set; without some formatting setter, `formatState()` returns the raw state and never reaches `getPrefix()`/`getSuffix()`, so the decorations would not render. The infolists `CanFormatState` has no such fast path, so `DecoratedEntry` has no `setUp()`; add the same identity closure there if Filament adds one.
- Views: one per decoration under `resources/views/`. `badge.blade.php` wraps `<x-filament::badge>` (colour default `primary`, size, icon, icon position, tooltip, `fi-font-*` classes for weight and family, and an inline `border-radius: 9999px` when pill). `icon.blade.php` wraps `Filament\Support\generate_icon_html()` in a `<span>` that carries the ARIA attributes and the inline colour; `trend.blade.php` uses the same helper for its arrow. The other views are a plain `<span>` or `<img>` with inline styles. Every view except the badge's gets its `x-tooltip` attribute from `partials/tooltip.blade.php`, included inside the root tag with `['tooltip' => $getTooltip()]`; the badge passes its tooltip to `<x-filament::badge>`. The Icon, Dot, and Color views get their screen-reader attributes the same way from `partials/screen-reader.blade.php`, with `['decorative' => $isDecorative(), 'label' => $getLabel()]`.

Constraints to keep:

- The public API mirrors the original component's (method names, argument shapes, defaults), so consumers switch by changing `use` statements and the two decoration class names. Don't rename or re-default a public method; add new ones.
- Don't give a class the short name of a Filament class. The original's `Icon` clashed with `Filament\Schemas\Components\Icon`. Importing the wrong one throws `Method ...::column does not exist` at render, and an infolist can't import both. That is why every decoration class ends in `Decoration`.
- Raw colours are accepted only in known formats. A raw colour comes from row data and lands in an inline `style`, where HTML escaping doesn't stop CSS injection, so `ColorDecoration::PATTERN` allows hex and functional colours of numbers only. Don't widen it to colour names, `var()`, or free text, and don't put any other row value into a `style` attribute without the same kind of check.
- `separator()` deliberately replaces the inherited list-separator meaning. It writes `$decorationSeparator` and leaves the parent's `$separator` null, so `getSeparator()` returns null and the state is never split. Keep `getSeparator()` untouched.
- Views use inline styles and Filament's own `fi-*` classes only, never Tailwind utilities, because consumers' themes don't compile this package's views and no `@source` line is asked of them.
- Rector must not change public or protected signatures: they must stay compatible with Filament's parents and with the mirrored API. When a rule wants to, skip that rule for that file in `rector.php` `withSkip()` (as done for `SafeDeclareStrictTypesRector` on `DecoratedColumn.php` and `DecoratedEntry.php`) instead of editing the signature.

## Tests

Pest through Orchestra Testbench (`tests/TestCase.php`): all Filament sub-package providers plus this package's are registered, the database is Testbench's `testing` connection (sqlite in memory) under `LazilyRefreshDatabase`, and migrations load from `tests/database/migrations`. Fixtures live under `tests/Fixtures`: `Client`, an Eloquent model on a `clients` table with a `name` column, and `ListClients`, a Livewire component using `InteractsWithTable` that renders a `DecoratedColumn` with an `IconDecoration` and a `BadgeDecoration`. `tests/DecoratedColumnTest.php` covers the API by calling `getPrefix()`/`getSuffix()` on a column given `->record($client)` and on an entry inside `Schema::make()->record($client)`, and renders one real table through `Livewire\Livewire::test(ListClients::class)->html()`, parsed with `DOMDocument` (pest-plugin-livewire is not installed, so there is no `livewire()` helper). Each newer decoration has its own file (`tests/DotDecorationTest.php`, `tests/ImageDecorationTest.php`, and so on) that asserts on the same `getPrefix()`/`getSuffix()` HTML, with data from closures that return fixed values or read `$record->name`. `tests/Pest.php` holds the shared helpers: `htmlXPath()` parses rendered HTML for XPath queries, and `trendArrow()` gives the path of a Heroicon arrow. `tests/DebugTest.php` is an arch test that fails if `dd`, `dump`, or `ray` appear anywhere.

## Style

Pint with the `laravel` preset plus `concat_space: one` (`'a' . $b`), `types_spaces: single` (`string | Closure | null`), a blank line before statements, and one trait per `use` statement. Rector runs the dead-code, code-quality, type-declaration, privatization, and early-return sets plus the PHP version sets on `src/`.

## Agent skills

### Issue tracker

Issues and specs live as local markdown files under `.scratch/<feature>/`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary: `needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`, recorded as a `Status:` line in each issue file. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
