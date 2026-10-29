# 02: Dot decoration

**What to build:** A developer can put a dot in either slot of a column or entry: a small, fixed-size filled circle in a palette colour that signals a status. When no colour is set, the dot is gray. Like an icon, a dot is decorative without a label and announced under its label with one. It supports a tooltip, `visible()` and `hidden()`, and colour closures that read `$record`.

This ticket also proves that a new decoration mixes with a badge in one slot.

See the spec's Dot user stories and "Per decoration: Dot": `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour)

**Status:** resolved

- [x] A dot with no colour renders as a gray mark, using the shared mark colour.
- [x] A dot given a palette colour, including through a closure that reads `$record`, renders in that palette's mark shade.
- [x] A dot without a label is hidden from screen readers. A dot with a label is exposed as an image named by that label.
- [x] A dot with a tooltip renders the shared tooltip markup.
- [x] A hidden dot renders nothing.
- [x] A dot and a badge in the same slot both render, in order, with the separator.
- [x] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [x] Pest, the Pint check, PHPStan and the Rector dry run all pass.

## Comments

Resolved in merge 20cde40.
