# 03: Image decoration

**What to build:** A developer can put a small picture from a URL in either slot, such as an avatar, a logo or a flag.

- `image()` takes the URL as a string, a closure that reads `$record`, or null.
- The image is square by default and fully rounded with `circular()`.
- It's one line of text tall (1.25rem) by default. `size()` takes a CSS length or a number of pixels.
- It fills its box without being stretched and loads lazily.
- Its alternative text is the label when labelled, and empty when decorative.
- A null URL means nothing to show. The image counts as hidden, so a slot holding only that image is empty and the owner shows its plain text with no separator.

See the spec's Image user stories and "Per decoration: Image": `.scratch/more-decorations/spec.md`.

**Blocked by:** 01 (Share the decorative rule, tooltip markup and palette-mark colour)

**Status:** ready-for-agent

- [ ] The URL from a closure that reads `$record` appears as the image source.
- [ ] Square by default, fully rounded with `circular()`.
- [ ] Default size is 1.25rem. A custom `size()` changes both width and height.
- [ ] The image covers its box without distortion and is lazily loaded.
- [ ] Alternative text is empty without a label and is the label with one.
- [ ] A null URL renders nothing. A slot holding only that image returns the owner's plain prefix or suffix with no separator.
- [ ] A tooltip renders the shared tooltip markup.
- [ ] Tests go through the column's prefix and suffix HTML, in a test file for this decoration.
- [ ] Pest, the Pint check, PHPStan and the Rector dry run all pass.
