# 07: Progress decoration

**What to build:** A developer can put a thin bar in either slot that shows a value against a maximum, for example account usage against its limit.

- **Input.** `value()` takes a number, a closure that reads `$record`, or null. `max()` takes a number or closure and defaults to 100.
- **Clamping.** The value is clamped between 0 and the maximum. A maximum of zero or less shows an empty bar.
- **Fill colour.** `color()` takes a palette colour, primary by default, using the shared mark colour.
- **Track.** Fixed size: about three line-heights wide, a few pixels tall, with rounded ends. It's a neutral gray that adapts to dark mode through `light-dark()`.
- **Screen readers.** When labelled, the bar is exposed as a progress bar with its value, minimum, maximum and label. When decorative, it's hidden from screen readers.
- **Null.** A null value means nothing to show, so the decoration counts as hidden.

See the spec's Progress user stories and "Per decoration: Progress": `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour)

**Status:** ready-for-agent

- [ ] The fill width is the value's percentage of the maximum, with both the default maximum and a custom one.
- [ ] A value above the maximum fills the bar completely. A value below zero leaves it empty.
- [ ] A maximum of zero or less shows an empty bar without an error.
- [ ] The fill is the primary mark shade by default, and the given palette's mark shade when coloured.
- [ ] With a label, the bar has progress-bar semantics: value, minimum, maximum and label. Without a label, it's hidden from screen readers.
- [ ] A null value renders nothing. A slot holding only that decoration has no separator.
- [ ] A tooltip renders the shared tooltip markup.
- [ ] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
