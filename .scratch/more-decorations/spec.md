# More decorations: Dot, Image, Text, Color, Trend, Progress

Status: ready-for-agent

## Problem Statement

A developer using this package can put only two kinds of decoration in a slot: a badge and an icon. Many everyday cells need something else beside the owner's text:

- a status dot before a name
- an avatar or logo before a name
- muted secondary text such as "(you)" or "#1042"
- a sample of a raw colour beside its hex code
- an up or down change beside a revenue figure
- a small bar beside a usage figure

Today they have to add a second column for some of these (Filament's image and colour columns), write their own Blade view, or build an HTML string for Filament's plain prefix or suffix. A second column splits one piece of information in two. A custom view or HTML string gives up what decorations already provide: closures with `$record`, tooltips, `visible()`/`hidden()`, separator handling and screen-reader behaviour.

## Solution

Six new kinds of decoration, each usable in either slot of a `DecoratedColumn` or a `DecoratedEntry`, alongside badges and icons:

- **Dot**: a small round mark in a palette colour, gray by default.
- **Image**: a small picture from a URL, square by default, round with `circular()`.
- **Text**: the decoration's label as secondary text, gray by default.
- **Color**: a small square showing a raw colour, which must be in a known colour format.
- **Trend**: a change shown as an arrow and a signed number, coloured by direction.
- **Progress**: a thin bar showing a value against a maximum.

Each one gets its data from its own closure, so a developer writes, for example, a `value()` closure that reads the row's `$record`. A decoration with nothing to show (a null image, a null change, an invalid raw colour, a null progress value) renders nothing. If every decoration in a slot renders nothing, the owner shows its plain text with no separator.

## User Stories

### Dot

1. As a developer, I want to put a dot before a client's name, so that the client's status shows without a separate column.
2. As a developer, I want to set the dot's palette colour with a closure that reads `$record`, so that each row shows its own status.
3. As a developer, I want the dot to be gray when I set no colour, so that an unconfigured dot looks neutral, like an unconfigured icon.
4. As a developer, I want to give the dot a tooltip, so that someone hovering can learn what the colour means.
5. As a developer, I want to hide the dot for some rows with `visible()` or `hidden()`, so that only the rows that need it show it.
6. As a screen-reader user, I want a dot with a label to be announced under that label, so that I get the status that sighted users get from the colour.
7. As a screen-reader user, I want a dot without a label to be skipped, so that I'm not told about a mark that adds nothing to the text beside it.

### Image

8. As a developer, I want to show the assignee's avatar before their name, so that people can recognise the assignee at a glance.
9. As a developer, I want to give the image's URL through `image()` with a closure that reads `$record`, so that each row shows its own picture.
10. As a developer, I want the image to render nothing when the URL is null, so that rows without a picture show just their text and no stray separator.
11. As a developer, I want the image square by default, so that logos and flags are not cropped into circles.
12. As a developer, I want `circular()` to make the image round, so that avatars look like avatars.
13. As a developer, I want the image one line of text tall by default, so that it doesn't change the row height.
14. As a developer, I want to change the image size with `size()`, so that I can fit a larger logo when the layout allows it.
15. As a developer, I want a non-square picture to fill its box without being stretched, so that photos keep their proportions.
16. As a screen-reader user, I want an image with a label to have that label as its alternative text, so that I know what the picture shows.
17. As a screen-reader user, I want an image without a label to be skipped, so that I'm not told about a picture that repeats the name beside it.

### Text

18. As a developer, I want to show muted text such as "(you)" after a user's name, so that the current user stands out without a badge.
19. As a developer, I want the text to come from `label()`, as a badge's does, so that I already know how to set it, including with a closure.
20. As a developer, I want the text gray by default, so that it reads as secondary to the owner's text.
21. As a developer, I want to give the text a palette colour, so that I can mark, for example, "overdue" in danger red.
22. As a developer, I want to set the text's weight and font family, so that an ID such as "#1042" can be monospace.
23. As a developer, I want the text to take the size of the text around it, so that it lines up with the owner's text without extra settings.
24. As a developer, I want the text escaped, so that a value from the database can't inject HTML.
25. As a panel user in dark mode, I want coloured text to stay readable, so that a decoration doesn't disappear against a dark background.

### Color

26. As a developer, I want to show a sample of a product's raw colour beside its hex code, so that people see the colour, not just the code.
27. As a developer, I want to give the raw colour through `value()` with a closure that reads `$record`, so that each row shows its own colour.
28. As a developer, I want hex, `rgb()`, `hsl()` and `oklch()` values accepted, so that colours stored in any common format work.
29. As a developer, I want any other value to render nothing, so that a malformed or malicious value can't inject CSS into the page.
30. As a developer, I want a null raw colour to render nothing, so that rows without a colour show just their text.
31. As a panel user, I want a very light raw colour, such as white, to stay visible, so that the sample doesn't vanish against the background.
32. As a screen-reader user, I want a colour sample with a label to be announced under that label, so that I know what the sample stands for.

### Trend

33. As a developer, I want to show a change beside a revenue figure, so that people see at a glance whether it went up or down.
34. As a developer, I want to give the change through `value()` with a closure that reads `$record`, so that each row shows its own change.
35. As a panel user, I want a rise shown with an up arrow, a plus sign and the success colour, so that good news reads as good.
36. As a panel user, I want a fall shown with a down arrow, a minus sign and the danger colour, so that bad news reads as bad.
37. As a panel user, I want a change of zero shown in gray with no arrow and no sign, so that "no change" doesn't look like a rise or a fall.
38. As a panel user, I want a change that rounds to zero shown as zero, so that "+0.0%" never appears in green.
39. As a developer, I want `inverse()` to swap the success and danger colours, so that a drop in churn or cost shows as good news.
40. As a developer, I want the change rounded to one decimal place by default, so that a long computed float doesn't overflow the cell.
41. As a developer, I want `decimals()` to change the rounding, so that whole-number changes can show without ".0".
42. As a developer, I want `%` as the default unit, so that the most common case needs no setting.
43. As a developer, I want `unit()` to change the unit or remove it with null, so that changes in points, euros or plain counts read correctly.
44. As a developer, I want a null change to render nothing, so that rows without a previous period show just their text.
45. As a screen-reader user, I want to hear the signed number and not the arrow, so that the direction comes across once and clearly.

### Progress

46. As a developer, I want to show a small bar beside a usage figure, so that people see how close an account is to its limit.
47. As a developer, I want to give the value through `value()` and the maximum through `max()`, both accepting closures, so that each row uses its own figures.
48. As a developer, I want the maximum to be 100 when I don't set one, so that a percentage needs no extra setting.
49. As a developer, I want values below zero or above the maximum clamped, so that the bar never overflows its track.
50. As a developer, I want a maximum of zero or less to show an empty bar, so that a bad maximum can't break the page.
51. As a developer, I want a null value to render nothing, so that rows without a figure show just their text.
52. As a developer, I want to set the bar's palette colour with a closure, so that I can turn it danger red when usage is high.
53. As a developer, I want the bar primary-coloured by default, so that an unconfigured bar matches the panel.
54. As a screen-reader user, I want a progress bar with a label to be announced as a progress bar with its value and maximum, so that I get the same information as sighted users.
55. As a screen-reader user, I want a progress bar without a label to be skipped, so that I'm not told twice about a figure the owner's text already shows.

### All six

56. As a developer, I want every new decoration to work in a `DecoratedEntry` exactly as it does in a `DecoratedColumn`, so that tables and infolists look the same.
57. As a developer, I want every new decoration to work in either slot, so that I choose the side.
58. As a developer, I want to mix the new decorations with badges and icons in one slot, so that a cell can show, for example, a dot and a badge together.
59. As a developer, I want every new decoration to accept `tooltip()`, `visible()` and `hidden()`, so that they behave like badges and icons.
60. As a developer, I want every setter to accept a closure that receives `$record`, `$livewire` and `$state`, so that I configure them the way I configure badges.
61. As a developer, I want a slot whose decorations all render nothing to show no separator, so that rows without data don't get a dangling dash.
62. As a developer, I want the new decorations to work without adding anything to my theme, so that installing the package is still a single Composer step.
63. As a developer reading the README, I want one section per new decoration with an example, so that I can copy one into my table.
64. As a developer upgrading the package, I want every existing public method to keep its name and behaviour, so that upgrading needs no code changes.

## Implementation Decisions

### Module shape

- Six new decoration classes, `DotDecoration`, `ImageDecoration`, `TextDecoration`, `ColorDecoration`, `TrendDecoration` and `ProgressDecoration`, each extending the shared decoration base. Each has its own Blade view in the package's view namespace.
- Each class has the `Decoration` suffix, so no short name clashes with a Filament class.
- No change to the slots, the separator or the owners. Owners already render any decoration through the base class.

### Shared behaviour

- **Decorative rule.** A decoration with no label is decorative and hidden from screen readers. With a label, it is announced under that label. Today the rule exists only on the icon decoration; lift it into the decoration base so Dot, Image, Color and Progress share it. The icon decoration keeps the same public method, now inherited.
- **Nothing to show.** A decoration with nothing to show counts as hidden, using the same visibility check the slots already use to skip hidden decorations. That covers Image with a null URL, Color with a null or rejected raw colour, Trend with a null change, and Progress with a null value. A slot holding only such decorations is empty, so the owner falls through to its plain prefix or suffix with no separator.
- **Tooltips.** Every new view supports `tooltip()` with the same tooltip markup the icon view uses. Put that markup in one shared place instead of copying it into each view.
- **Data closures.** Each decoration takes its data through its own setter. The owner's state is not passed in, and `$state` inside a decoration's closures keeps its current meaning: the decoration's own label. Every setter accepts a closure resolved with `$record`, `$livewire` and `$state`, as the existing setters are.
- **Palette colours.** The new decorations take palette colours by name, such as `success`, `danger` or `gray`. Marks (Dot, Progress fill) use the shades the icon decoration uses: gray 400 by default and shade 500 for any other palette. Text-like output (Text, Trend) uses the shades Filament uses for coloured text: shade 600 in light mode and 400 in dark mode, through the CSS `light-dark()` function, which follows the `color-scheme` that Filament's panel sets in dark mode. Gray text uses shade 500 in both modes.
- **Styling.** Views use only inline styles and Filament's own `fi-*` classes and Blade components, never Tailwind utilities, as the existing views do.

### Per decoration

- **Dot.** It has no data setter. It's a filled circle of a fixed size, about half a line tall, in its palette colour (`color()`, gray by default). There is no size setter.
- **Image.**
  - `image()` takes a URL string, a closure, or null.
  - `size()` takes a CSS length string or a number of pixels and defaults to 1.25rem. It sets both width and height.
  - `circular()` takes a boolean or closure, defaults to true when called, and makes the image fully rounded. Without it the image is square.
  - The image covers its box without distortion and loads lazily.
  - Alternative text is the label when labelled, and empty when decorative. An empty alternative text is the standard way to hide an image from screen readers.
- **Text.**
  - It shows its label, escaped, the same way a badge does. When no label is set, the label comes from the decoration's name.
  - It's gray by default, and `color()` takes a palette colour.
  - `weight()` and `fontFamily()` use Filament's existing concerns and `fi-font-*` classes, as the badge does.
  - It has no size setter and takes the size of the text around it.
- **Color.**
  - `value()` takes a raw colour string, a closure, or null.
  - These raw colour forms are accepted: `#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa`, and `rgb()`, `rgba()`, `hsl()`, `hsla()` or `oklch()` containing only numbers, units, spaces, commas and slashes. Anything else counts as nothing to show.
  - The inherited `color()` is ignored.
  - It's a square of a fixed size, about one line tall, with slightly rounded corners and a thin neutral border so that very light colours stay visible.
- **Trend.**
  - `value()` takes the change as a number, a closure, or null.
  - `decimals()` sets how many decimal places the change is rounded to, defaulting to 1.
  - `unit()` takes a string or null, defaulting to `%`. The unit is appended exactly as given, with no space added.
  - `inverse()` takes a boolean or closure, defaulting to true when called.
  - Direction is decided after rounding. Above zero shows an up arrow, a plus sign and success colour. Below zero shows a down arrow, a minus sign and danger colour. Zero shows gray with no arrow and no sign. `inverse()` swaps success and danger.
  - The arrow is a Heroicon rendered through Filament's icon helper and hidden from screen readers. The signed number carries the direction.
  - Number formatting must not need the `intl` extension, because the package doesn't require it. It uses a plain decimal point, with no locale formatting.
- **Progress.**
  - `value()` takes a number, a closure, or null. `max()` takes a number or closure and defaults to 100.
  - The value is clamped between 0 and the maximum. A maximum of zero or less shows an empty bar.
  - `color()` takes a palette colour, primary by default.
  - It's a fixed-size track, about three line-heights wide and a few pixels tall, with rounded ends. The fill width is the clamped percentage.
  - The track is a neutral gray that adapts to dark mode in the same way as text colours.
  - When labelled, it's exposed as a progress bar with its value, minimum, maximum and label. When decorative, it's hidden from screen readers.

### API constraints

- Only new public methods are added. No existing public method is renamed, re-defaulted or removed.
- The code must work across the CI matrix: PHP 8.2 to 8.4 and Laravel 11 to 13. If the code starts using a newer Filament API, re-check the Filament floor in Composer.
- Rector must not change public or protected signatures. Skip the rule for that file instead, as the project already does.

### Documentation

- The README gets one section per new decoration, after the existing Badges and Icons sections, each with a short example. The closure-parameters section mentions the new data setters.
- The architecture section of the agent guide describes the new decorations and the shared decorative rule.
- The CHANGELOG is left to the release workflow.

## Testing Decisions

- **Seam.** Tests go through one existing seam: the HTML that a `DecoratedColumn`'s `getPrefix()`/`getSuffix()` returns, for a column given a record and decorations in a slot. No new seams, no new database columns or fixtures, and no change to the real-table test. Values come from closures that return fixed values or read `$record->name`.
- **What a good test looks like.** It sets up a column the way a consumer would and asserts on the rendered HTML: the text, attributes, inline colour, presence or absence of the separator, and accessibility attributes. It never calls a decoration's getters directly.
- **Dot.** Gray mark by default and palette mark when coloured. Hidden from screen readers without a label, and announced under its label with one. Tooltip present.
- **Image.**
  - The URL from a `$record` closure appears.
  - Square by default and rounded with `circular()`.
  - Default and custom size.
  - Empty alternative text without a label, and the label as alternative text with one.
  - A null URL leaves the slot empty: the parent prefix or suffix comes back with no separator.
- **Text.** The label from a `$record` closure appears, and HTML in it is escaped. Gray by default, palette colour when set. Weight and font-family classes.
- **Color.** Each accepted format renders the raw colour. An injection attempt and a plain word both render nothing. The inherited `color()` doesn't change the sample. Labelled and decorative screen-reader behaviour.
- **Trend.**
  - Rise, fall and zero, each with the right arrow, sign and colour.
  - A value that rounds to zero is treated as zero.
  - `inverse()` swaps the colours.
  - `decimals()` and `unit()`, including a null unit.
  - A null change renders nothing.
- **Progress.**
  - The fill percentage for a normal value, with the default maximum and with a custom one.
  - Clamping above the maximum and below zero.
  - A maximum of zero or less.
  - A null value renders nothing.
  - Progress-bar attributes with a label, hidden from screen readers without one.
- **Mixed slot.** One test places a new decoration next to a badge in the same slot.
- **Prior art.** The existing decoration tests: badges and icons through `getPrefix()`/`getSuffix()`, the unlabelled icon hidden from screen readers, gray and palette icon tints, and the infolist entry test inside a schema with a record. One test file per decoration kind, following that file's style.
- **Gates.** Pest, Pint check, PHPStan level 4 and the Rector dry run all pass.

## Out of Scope

- The other ideas from the brainstorm: copy and link decorations, a sparkline, a spinner, and a generic view decoration.
- Passing the owner's state into decoration closures.
- Images from a storage disk and path, placeholder images, and fallbacks when an image fails to load.
- Size settings for Dot, Color and Progress.
- A text label or colour thresholds on Progress. A colour closure covers thresholds.
- Computing a Trend from a current and a previous value, and locale-aware number formatting.
- Array (custom) palettes on the new decorations. Palette colours are given by name.
- Changes to the real-table fixture or the database schema used in tests.

## Further Notes

- The project's terms (Owner, Slot, Separator, Decorative, Palette colour, Raw colour, Change, and the decoration kinds) are defined in `GLOSSARY.md`.
- These choices came up while writing the spec, not during the interview. Review them before implementing:
  - A null Progress value renders nothing, matching Image and Trend.
  - A maximum of zero or less shows an empty bar.
  - Image alternative text is empty when decorative.
  - Trend uses a Heroicon arrow and the minus sign "−".
  - Text-like colours use `light-dark()` for dark mode, with gray at shade 500.
  - The decorative rule moves to the decoration base.
- `light-dark()` follows the `color-scheme` that Filament's panel sets on dark mode. Outside a panel, or in browsers without `light-dark()`, text falls back to the light-mode shade or inherits its colour, which is acceptable.
- A raw colour from the database is untrusted input in an inline style. HTML escaping does not stop CSS injection, which is why only known colour formats are accepted.
- The image `size()` value is set by the developer, not taken from row data, so it is not validated.
