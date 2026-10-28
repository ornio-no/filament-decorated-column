# 05: Color decoration

**What to build:** A developer can put a sample of a raw colour in either slot, for example beside a product's hex code.

- **Input.** `value()` takes the raw colour as a string, a closure that reads `$record`, or null.
- **Accepted formats.** `#rgb`, `#rgba`, `#rrggbb` and `#rrggbbaa`, plus `rgb()`, `rgba()`, `hsl()`, `hsla()` and `oklch()` containing only numbers, units, spaces, commas and slashes.
- **Anything else.** Any other value, or null, means nothing to show. The decoration counts as hidden, so a malformed or malicious value can never reach the inline style.
- **Inherited `color()`.** Ignored.
- **Look.** A fixed-size square about one line tall, with slightly rounded corners and a thin neutral border so that white and very light colours stay visible.
- **Screen readers.** Decorative without a label, announced under its label with one.

See the spec's Color user stories, "Per decoration: Color" and the CSS-injection note: `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour)

**Status:** ready-for-agent

- [ ] Each accepted format renders a square filled with that raw colour.
- [ ] A CSS-injection attempt (for example a value that closes the declaration and adds another) renders nothing.
- [ ] A plain word that isn't one of the accepted formats (for example `red`) renders nothing.
- [ ] Null renders nothing. A slot holding only that decoration has no separator.
- [ ] Setting the inherited `color()` doesn't change the sample.
- [ ] The square has a thin neutral border.
- [ ] Hidden from screen readers without a label, and exposed as an image named by its label with one.
- [ ] A tooltip renders the shared tooltip markup.
- [ ] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
