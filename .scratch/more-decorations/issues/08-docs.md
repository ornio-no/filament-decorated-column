# 08: Document the new decorations

**What to build:** A developer reading the README can find and copy each new decoration, and an agent reading the agent guide understands how the decorations are built.

- **README.** One section per new decoration (Dot, Image, Text, Color, Trend, Progress), after the existing Badges and Icons sections. Each section has a short example and lists its settings and defaults. The closure-parameters section mentions the new data setters (`value()`, `max()`, `image()`) and that they receive `$record`, `$livewire` and `$state` like every other setter. The introduction and usage examples mention the new decorations where they currently list only badges and icons.
- **Agent guide.** The architecture section describes the six new decorations, the shared decorative rule on the decoration base, the shared tooltip markup and colour logic, and the "nothing to show counts as hidden" behaviour. Its constraint list notes that raw colours are accepted only in known formats.
- **CHANGELOG.** Left alone. The release workflow fills it in.

Use the terms defined in `GLOSSARY.md`: Owner, Slot, Separator, Decorative, Palette colour, Raw colour and Change.

See the spec's "Documentation" section: `.scratch/more-decorations/spec.md`.

**Blocked by:** 02 (Dot decoration), 03 (Image decoration), 04 (Text decoration), 05 (Color decoration), 06 (Trend decoration), 07 (Progress decoration)

**Status:** resolved

- [x] The README has a section for each of the six new decorations, with an example that matches the built API.
- [x] The README's closure-parameters section covers the new data setters.
- [x] The README's introduction no longer implies that badges and icons are the only decorations.
- [x] The agent guide's architecture section describes the new decorations and the shared behaviour.
- [x] The CHANGELOG is unchanged.
- [x] Every example in the README uses a method that exists, with its real defaults.

## Comments

Resolved in merge b4b104d. Code review (merge 41d07ca) removed the Text font limitation note, because the fonts now work.
