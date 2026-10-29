# Changelog

All notable changes to `filament-decorated-column` will be documented in this file.

## v1.1.1 - 2026-10-29

Documentation only, with no code changes.

The README gains a banner and a one-line summary under the title, and fixes two mistakes: a `DotDecoration` is 0.5rem across, and an `ImageDecoration` `size()` given a unitless string such as `'32'` falls back to the default, while an integer still counts as pixels. The rest of the README is copy-edited for clarity, with exact values for the separator's opacity and the trend colours.

## v1.1.0 - 2026-10-29

Six new decorations sit beside badges and icons in either slot of `DecoratedColumn` and `DecoratedEntry`:

- `DotDecoration`: a small round mark in a palette colour, for a status.
- `ImageDecoration`: a small picture from a URL, such as an avatar or a logo, square or `circular()`.
- `TextDecoration`: secondary text such as "(you)", gray by default, with its own colour, weight and font family.
- `ColorDecoration`: a sample of a raw colour from your data. Only hex, `rgb()`, `hsl()` and `oklch()` values are accepted, because the value goes into an inline style.
- `TrendDecoration`: a change shown as an arrow and a signed number, coloured by direction, with `inverse()` for metrics where down is good.
- `ProgressDecoration`: a thin bar showing a value against a maximum.

A decoration with nothing to show, such as a null image URL or a null change, renders nothing, and a slot left empty drops its separator. The README covers each decoration's settings, with light and dark screenshots.

Fixes:

- An HTML tooltip now shows on older Laravel 11 releases, whose `@js()` encoded an `HtmlString` as an empty object.
- A colour name passed to `color()` goes into an inline style, so a name holding anything but lowercase letters, digits and hyphens now draws gray.

## v1.0.0 - 2026-10-20

Initial release. `DecoratedColumn` and `DecoratedEntry` are Filament 5 `TextColumn` and `TextEntry` with badge and icon decorations (`BadgeDecoration`, `IconDecoration`) before or after the text.
