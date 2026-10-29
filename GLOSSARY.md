# Filament Decorated Column

Small visual marks placed before or after the text of a Filament table column or infolist entry.

## Language

### Structure

**Owner**:
The table column or infolist entry that holds decorations and shows its own text beside them.
_Avoid_: Parent, host

**Decoration**:
One small visual mark shown beside an owner's text, such as a badge, an icon or a dot.
_Avoid_: Badge (as the generic word), affix, item

**Slot**:
The place on one side of the owner's text that holds decorations: the prefix slot before it, the suffix slot after it.
_Avoid_: Affix

**Separator**:
The mark between the owner's text and a non-empty slot, an em dash by default. It never splits the owner's value into several items.
_Avoid_: Divider

**Decorative**:
A decoration with no label, hidden from screen readers because the owner's text already says what it means.
_Avoid_: Hidden (a hidden decoration is not rendered at all)

### Kinds of decoration

**Badge**:
A decoration showing its label inside a coloured rounded box, optionally with an icon.

**Icon**:
A decoration showing a single icon in a palette colour.

**Dot**:
A small round decoration in a palette colour that signals a status.
_Avoid_: Indicator, bullet

**Image**:
A small picture from a URL, such as an avatar, a logo or a flag.
_Avoid_: Avatar (that is only one use)

**Text**:
A decoration showing its label as secondary text, gray unless given a palette colour.
_Avoid_: Note, caption

**Color**:
A small square decoration showing a raw colour as a sample of itself.
_Avoid_: Swatch, colour (alone, since every decoration has a palette colour)

**Trend**:
A decoration showing a change as an arrow and a signed number, coloured by direction.

**Change**:
How much a number went up or down, the value a trend shows.
_Avoid_: Delta, diff

**Progress**:
A thin bar showing how far a value has come toward its maximum.
_Avoid_: Meter, gauge

### Colour

**Palette colour**:
A named Filament colour, such as `success`, `danger` or `gray`, chosen to carry meaning.

**Raw colour**:
A colour value that comes from the data itself, such as `#ff6600`, shown as itself rather than as a meaning.
_Avoid_: Hex (the value may also be `rgb()`, `hsl()` or `oklch()`)
