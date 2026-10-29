# 01: Badge weight() and fontFamily() do nothing

**What to build:** A developer who sets `weight()` or `fontFamily()` on a `BadgeDecoration` sees the badge's text change. Today it doesn't change.

**Cause.** The badge view puts Filament's `fi-font-*` classes (`fi-font-bold`, `fi-font-mono`) on `<x-filament::badge>`. Filament's stylesheet defines those classes only on its own text items and links (`.fi-in-text-item`, `.fi-ta-text-item`, `.fi-sc-text`, `.fi-link`), never on `.fi-badge`. So both settings have no effect unless the consumer's theme styles `.fi-badge.fi-font-*`. The README currently documents this as a limitation in the Badges section.

`TextDecoration` had the same bug and was fixed during the more-decorations review (merge 41d07ca on `oxodesign/lombok-strait`). It now renders a `FontWeight` enum as an inline numeric `font-weight`, and a `FontFamily` enum as an inline `font-family` that reads the panel's `--font-family`, `--serif-font-family` and `--mono-font-family` variables with generic fallbacks. Any other string stays a class. That mapping currently lives inside the text view.

**Fix.** Give the badge the same inline font styles. Move the enum-to-CSS mapping out of the text view into one shared place, and have the text and badge views both use it. Merge the result with the badge's existing pill style.

**Compatibility.** The default weight stays `FontWeight::Medium`. Filament's `.fi-badge` already applies `font-medium` (500), so a badge without settings looks the same as before. The public API doesn't change.

**Blocked by:** None (can start immediately)

**Status:** ready-for-agent

- [ ] A badge with `weight(FontWeight::Bold)` renders `font-weight: 700` inline. Every `FontWeight` case maps to its number (100–900).
- [ ] A badge with `fontFamily(FontFamily::Mono)` renders an inline `font-family` that uses the panel's mono font variable, with generic fallbacks. Sans and Serif work the same way.
- [ ] A string that isn't a `FontWeight` or `FontFamily` value stays a class on the badge, as now.
- [ ] A badge with no font settings renders `font-weight: 500`, or nothing, and looks the same as before.
- [ ] A pill badge keeps its `border-radius` and `padding-inline` alongside the font styles.
- [ ] The text and badge views share one font mapping, and `TextDecoration`'s tests still pass unchanged.
- [ ] The README's Badges section drops the "only where your theme styles `.fi-badge.fi-font-*`" limitation and describes the inline styles, matching the Text section.
- [ ] Tests go through the column's prefix and suffix HTML, as the existing badge tests do.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
