# 06: Trend decoration

**What to build:** A developer can put a change beside a number in either slot, shown as an arrow and a signed number coloured by direction.

- **Input.** `value()` takes the change as a number, a closure that reads `$record`, or null.
- **Rounding.** The change is rounded to `decimals()` places, 1 by default, with a plain decimal point and no `intl` extension.
- **Unit.** `unit()` defaults to `%`. It's appended exactly as given, with no space added, and null removes it.
- **Direction.** Direction is decided after rounding:

  | Rounded change | Arrow | Sign | Colour |
  |---|---|---|---|
  | above zero | up | plus | success |
  | below zero | down | minus "−" | danger |
  | zero | none | none | gray |

- **`inverse()`.** Swaps success and danger, for metrics where down is good.
- **Arrow.** A Heroicon rendered through Filament's icon helper and hidden from screen readers. The signed number carries the direction.
- **Text colour.** Uses the shared text-colour logic from the text decoration.
- **Null.** A null change means nothing to show, so the decoration counts as hidden.

See the spec's Trend user stories and "Per decoration: Trend": `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour), 04 (Text decoration)

**Status:** ready-for-agent

- [ ] A rise renders an up arrow, a plus sign and the success text colour, for example "+12.5%".
- [ ] A fall renders a down arrow, a minus sign "−" and the danger text colour.
- [ ] Zero renders in gray with no arrow and no sign.
- [ ] A value that rounds to zero (for example 0.04 with 1 decimal) renders as zero.
- [ ] `inverse()` swaps the success and danger colours.
- [ ] `decimals()` changes the rounding. `unit()` changes the unit, and null removes it.
- [ ] The arrow is hidden from screen readers.
- [ ] A null change renders nothing. A slot holding only that decoration has no separator.
- [ ] Number formatting doesn't depend on the `intl` extension.
- [ ] A tooltip renders the shared tooltip markup.
- [ ] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
