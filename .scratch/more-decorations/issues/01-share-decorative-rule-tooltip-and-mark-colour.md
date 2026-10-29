# 01: Share the decorative rule, tooltip markup and palette-mark colour

**What to build:** A prefactor that makes the new decorations easy to add. Move three things that today belong only to the icon decoration into shared places, then switch the icon decoration over to them:

1. **Decorative rule.** A decoration with no label is decorative and hidden from screen readers. With a label, it's announced under that label. Lift this rule into the decoration base.
2. **Tooltip markup.** Put the tooltip markup the icon view uses in one shared place that every decoration view can include.
3. **Mark colour.** Put the colour logic for a mark in one shared place: gray shade 400 when no palette colour is set, and shade 500 of any other named palette colour.

Icons must look and behave exactly as before. The icon decoration keeps its public decorative method, now inherited.

See the spec's "Shared behaviour" section: `.scratch/more-decorations/spec.md`.

**Blocked by:** None (can start immediately)

**Status:** resolved

- [x] The decorative rule lives on the decoration base and is reachable from every decoration, including the icon decoration, under the same public method name as today.
- [x] The tooltip markup exists once and the icon view uses it.
- [x] The mark-colour logic exists once and the icon view uses it.
- [x] No public method is renamed, re-defaulted or removed.
- [x] Every existing test passes unchanged, including the unlabelled icon being hidden from screen readers and the gray and palette icon tints.
- [x] Pest, the Pint check, PHPStan and the Rector dry run all pass.

## Comments

Resolved in merge 4780e6e on `oxodesign/lombok-strait`. Code review (merge 41d07ca) also moved the screen-reader attributes into a shared partial and added a palette-name check to the shared mark colour.
