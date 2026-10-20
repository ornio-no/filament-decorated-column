# Changelog

All notable changes to `filament-decorated-column` will be documented in this file.

## v1.0.0 - 2026-10-20

Initial release. `DecoratedColumn` and `DecoratedEntry` are Filament 5 `TextColumn` and `TextEntry` with badge and icon decorations before or after the text. Ported from fatura-app's `App\Filament\Components\Decorated` with the same public API, except that its `Badge` and `Icon` are named `BadgeDecoration` and `IconDecoration` here so they don't clash with Filament's `Filament\Schemas\Components\Icon`. Switching means changing the `use` statements and renaming the `Badge::make()` and `Icon::make()` calls.
