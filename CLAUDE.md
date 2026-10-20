# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`ornio/filament-decorated-column` is a Filament v5 plugin package ("Display additional decorated context"), built on `spatie/laravel-package-tools`. It is still the unmodified plugin skeleton: `src/FilamentDecoratedColumn.php`, the command, the testing mixin, the config, the migration stub, and the README usage section are placeholders. No column class exists yet.

## Commands

```bash
composer install && npm install

composer test                         # Pest (vendor/bin/pest)
vendor/bin/pest tests/ExampleTest.php # one file
vendor/bin/pest --filter="can test"   # one test by name
composer lint                         # Pint, fixes in place
composer test:lint                    # Pint, check only
composer analyse                      # PHPStan level 4 on src/, config/, database/
composer refactor                     # Rector on src/ (composer test:refactor = dry run)

npm run build                         # esbuild resources/js/index.js -> resources/dist/filament-decorated-column.js
npm run dev                           # same, watch mode
```

CI runs Pest across PHP 8.2–8.4 × Laravel 11–13 (L13 needs PHP ≥ 8.3), both `prefer-lowest` and `prefer-stable`, on Linux and Windows. Keep code compatible with that whole range. A CI workflow runs Pint on every push and auto-commits a "Fix styling" commit.

## Architecture

- **`FilamentDecoratedColumnServiceProvider`** (spatie `PackageServiceProvider`) does all wiring. It registers config, migrations, translations, and views only when the matching file or directory exists. In `packageBooted()` it registers Filament assets, script data, icons, publishable stubs from `stubs/`, and the Livewire `Testable` mixin. Extend the protected `get*()` methods (`getAssets`, `getIcons`, `getCommands`, `getMigrations`, ...) rather than editing `configurePackage`.
- **`FilamentDecoratedColumnPlugin`** is the Filament panel plugin (id `filament-decorated-column`). `register()`/`boot()` are empty.
- **Frontend assets**: `bin/build.js` builds only the JS entry. The `Js`/`Css`/`AlpineComponent` registrations in `getAssets()` are commented out. Uncomment the matching line after producing the file in `resources/dist/`. `resources/css/index.css` has no build step yet. Users of the plugin are told to add `@source '../../../../vendor/ornio/filament-decorated-column/resources/**/*.blade.php'` to their Filament theme, so Tailwind classes in Blade views get picked up from the consumer's theme build.
- **Tests** use Orchestra Testbench (`tests/TestCase.php`) with all Filament sub-package providers registered, `LazilyRefreshDatabase`, and migrations loaded from `database/migrations`. `tests/DebugTest.php` is an arch test that fails if `dd`, `dump`, or `ray` appear anywhere.

## Skeleton naming mismatches

The package name is `filament-decorated-column`, but several files use `decorated-column`. These silently break:

- `config/decorated-column.php`: the provider looks for `config/{shortName}.php`, which is `config/filament-decorated-column.php`. As named, the config is never registered or publishable.
- `getMigrations()` returns `create_filament-decorated-column_table`, but the stub is `database/migrations/create_decorated_column_table.php.stub`.
- Translations live in `resources/lang/en/decorated-column.php` and resolve as `filament-decorated-column::decorated-column.<key>`.

Fix or delete these when the real implementation starts. If the package needs no database table, remove the migration stub, the `hasMigrations`/install-command migration steps, and `database/factories`.

## Style

Pint with the `laravel` preset plus `concat_space: one` (`'a' . $b`), a blank line before statements, and one trait per `use` statement. Rector runs dead-code, code-quality, type-declaration, privatization, early-return, and strict-boolean sets on `src/`.

## Agent skills

### Issue tracker

Issues and specs live as local markdown files under `.scratch/<feature>/`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary: `needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`, recorded as a `Status:` line in each issue file. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
