# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`ornio/filament-decorated-column` is a Filament v5 standalone component package: `DecoratedColumn` (extends `TextColumn`) and `DecoratedEntry` (extends `TextEntry`) render badge and icon decorations (`BadgeDecoration`, `IconDecoration`) before or after their text. It started as a port of an app-local `Decorated` component, with the namespace, the view names, and the decoration class names changed (the original's `Badge` and `Icon`), and `getPrefix()`/`getSuffix()` widened to Filament 5.10's signature (see Architecture). There is no Plugin class, config, migration, translation, or JS/CSS build. `FilamentDecoratedColumnServiceProvider` (spatie `PackageServiceProvider`, auto-discovered) registers only the package name and the `filament-decorated-column::` view namespace. Filament's plugin docs say "The Plugin object is only used for Panel Providers. Standalone Plugins do not use this object.", so consumers have no `->plugin()` step: the column and entry work in any table or infolist, in or out of a panel.

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

CI runs Pest across PHP 8.2 to 8.4 and Laravel 11 to 13 (Laravel 13 needs PHP 8.3 or later), under both `prefer-lowest` and `prefer-stable`, on Linux and Windows. Keep code compatible with that whole range. The `filament/filament` floor in `composer.json` was found by running the suite under `prefer-lowest`; re-check it whenever the code starts using a newer Filament API. A CI workflow runs Pint on every push and auto-commits a "Fix styling" commit.

## Architecture

- `Decoration` (abstract, extends Filament's `ViewComponent`) is one item of a prefix or suffix slot. It has a name, a label (derived from the name when unset, through Filament's tables `HasLabel`), a colour, a tooltip, and `visible()`/`hidden()`. `HasDecorations` hands it the owning column or entry through `column()` before rendering, and `resolveDefaultClosureDependencyForEvaluationByName()` resolves `$record` (the owner's record), `$state` (the decoration's own label, so a `$state` parameter inside `label()` recurses), and `$livewire` (the owner's Livewire component) from that owner.
- `BadgeDecoration` adds size (default `Size::Small`), weight (default `FontWeight::Medium`, applied in the view), font family, icon, icon position (default before), and `isPill()`. `shouldBePill()` returns the badge's own value when it is not null and otherwise the owner's `shouldBePills()`.
- `IconDecoration` adds icon and size (default `IconSize::Small`). `isDecorative()` is true when no `label()` was given; the view then renders `aria-hidden="true"` instead of `role="img"` plus `aria-label`. The colour is set inline (`var(--gray-400)` by default, shade 500 for any other palette) because Filament only tints icons that are direct children of a text cell.
- `Concerns\HasDecorations` (used by `DecoratedColumn` and `DecoratedEntry`) owns the slots: `prefixDecorations()`/`suffixDecorations()` with `prefixBadges()`/`suffixBadges()` as aliases, `asPills()`, and `separator()`. It overrides `getPrefix()`/`getSuffix()`. `renderDecorations()` renders the slot's visible decorations and strips Livewire's block comments. A non-empty slot is wrapped in an inline-flex `<span>`, followed by the separator mark and the parent's affix, and returned as an `HtmlString`. An empty slot falls through to the parent. The signatures take `(mixed $state = null, ?Model $relatedRecord = null)` and forward `...func_get_args()`, because Filament 5.10 added those parameters to the parent while earlier 5.x parents take none; the docblock on `getPrefix()` explains the forwarding.
- `DecoratedColumn::setUp()` pins `formatStateUsing()` to identity. Filament's tables `CanFormatState::hasStateFormatting()` checks the raw `$prefix`/`$suffix` properties, which decorations never set; without some formatting setter, `formatState()` returns the raw state and never reaches `getPrefix()`/`getSuffix()`, so the decorations would not render. The infolists `CanFormatState` has no such fast path, so `DecoratedEntry` has no `setUp()`; add the same identity closure there if Filament adds one.
- Views: `resources/views/badge.blade.php` wraps `<x-filament::badge>` (colour default `primary`, size, icon, icon position, tooltip, `fi-font-*` classes for weight and family, and an inline `border-radius: 9999px` when pill). `resources/views/icon.blade.php` wraps `Filament\Support\generate_icon_html()` in a `<span>` that carries the ARIA attributes, the inline colour, and an optional `x-tooltip`.

Constraints to keep:

- The public API mirrors the original component's (method names, argument shapes, defaults), so consumers switch by changing `use` statements and the two decoration class names. Don't rename or re-default a public method; add new ones.
- Don't give a class the short name of a Filament class. The original's `Icon` clashed with `Filament\Schemas\Components\Icon`. Importing the wrong one throws `Method ...::column does not exist` at render, and an infolist can't import both. That is why the decorations are `BadgeDecoration` and `IconDecoration`.
- `separator()` deliberately replaces the inherited list-separator meaning. It writes `$decorationSeparator` and leaves the parent's `$separator` null, so `getSeparator()` returns null and the state is never split. Keep `getSeparator()` untouched.
- Views use inline styles and Filament's own `fi-*` classes only, never Tailwind utilities, because consumers' themes don't compile this package's views and no `@source` line is asked of them.
- Rector must not change public or protected signatures: they must stay compatible with Filament's parents and with the mirrored API. When a rule wants to, skip that rule for that file in `rector.php` `withSkip()` (as done for `SafeDeclareStrictTypesRector` on `DecoratedColumn.php` and `DecoratedEntry.php`) instead of editing the signature.

## Tests

Pest through Orchestra Testbench (`tests/TestCase.php`): all Filament sub-package providers plus this package's are registered, the database is Testbench's `testing` connection (sqlite in memory) under `LazilyRefreshDatabase`, and migrations load from `tests/database/migrations`. Fixtures live under `tests/Fixtures`: `Client`, an Eloquent model on a `clients` table with a `name` column, and `ListClients`, a Livewire component using `InteractsWithTable` that renders a `DecoratedColumn` with an `IconDecoration` and a `BadgeDecoration`. `tests/DecoratedColumnTest.php` covers the API by calling `getPrefix()`/`getSuffix()` on a column given `->record($client)` and on an entry inside `Schema::make()->record($client)`, and renders one real table through `Livewire\Livewire::test(ListClients::class)->html()`, parsed with `DOMDocument` (pest-plugin-livewire is not installed, so there is no `livewire()` helper). `tests/DebugTest.php` is an arch test that fails if `dd`, `dump`, or `ray` appear anywhere.

## Style

Pint with the `laravel` preset plus `concat_space: one` (`'a' . $b`), `types_spaces: single` (`string | Closure | null`), a blank line before statements, and one trait per `use` statement. Rector runs the dead-code, code-quality, type-declaration, privatization, and early-return sets plus the PHP version sets on `src/`.

## Agent skills

### Issue tracker

Issues and specs live as local markdown files under `.scratch/<feature>/`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary: `needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`, recorded as a `Status:` line in each issue file. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
