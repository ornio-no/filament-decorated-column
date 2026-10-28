# 04: Text decoration

**What to build:** A developer can put secondary text, such as "(you)" or "#1042", in either slot.

- **Content.** The text is the decoration's label, escaped. It's set with `label()`, including through a closure, the same way a badge's is. When no label is set, it comes from the decoration's name.
- **Colour.** The text is gray by default and takes a palette colour through `color()`.
- **Style.** `weight()` and `fontFamily()` use Filament's existing settings and `fi-*` font classes, as the badge does. The text has no size setting and takes the size of the text around it.

This ticket adds the shared text-colour logic that the trend decoration reuses. Gray text uses shade 500 in both modes. Any other palette uses shade 600 in light mode and 400 in dark mode, through the CSS `light-dark()` function, which follows the colour scheme Filament's panel sets in dark mode.

See the spec's Text user stories, "Palette colours" and "Per decoration: Text": `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour)

**Status:** ready-for-agent

- [ ] The label from a closure that reads `$record` appears. With no label, the name-derived label appears.
- [ ] HTML in the label is escaped.
- [ ] Gray text uses shade 500. Palette text uses `light-dark()` with shades 600 and 400.
- [ ] The text-colour logic is shared, so another decoration can reuse it.
- [ ] Weight and font-family settings add Filament's `fi-font-*` classes.
- [ ] No font size is set on the text.
- [ ] A tooltip renders the shared tooltip markup.
- [ ] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
