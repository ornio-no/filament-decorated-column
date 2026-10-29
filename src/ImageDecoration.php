<?php

namespace Ornio\FilamentDecoratedColumn;

use Closure;

/**
 * A small picture from a URL beside the column text, such as an avatar,
 * a logo or a flag.
 */
class ImageDecoration extends Decoration
{
    /**
     * A non-negative number with a CSS length or percentage unit, such as
     * '12px', '1.5rem' or '50%'. \z, not $, so a trailing newline is
     * rejected too.
     */
    private const LENGTH_PATTERN = '/\A(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)(?:px|r?em|ch|ex|vw|vh|vmin|vmax|cm|mm|in|pt|pc|%)\z/i';

    private const DEFAULT_SIZE = '1.25rem';

    protected string $view = 'filament-decorated-column::image';

    protected string | Closure | null $image = null;

    protected string | int | Closure | null $size = null;

    protected bool | Closure $isCircular = false;

    public function image(string | Closure | null $url): static
    {
        $this->image = $url;

        return $this;
    }

    /**
     * Fully rounded, for avatars. Without it the image is square.
     */
    public function circular(bool | Closure $condition = true): static
    {
        $this->isCircular = $condition;

        return $this;
    }

    /**
     * Width and height: a CSS length such as '2rem', or a number of pixels.
     */
    public function size(string | int | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->evaluate($this->image);
    }

    /**
     * The CSS length of the image's width and height, one line of text
     * (1.25rem) by default. The size lands in an inline style and may come
     * from row data through a closure, so anything but an int or a length
     * in a known unit falls back to the default.
     */
    public function getSize(): string
    {
        $size = $this->evaluate($this->size);

        if (is_int($size)) {
            return $size . 'px';
        }

        if (is_string($size) && preg_match(self::LENGTH_PATTERN, $size) === 1) {
            return $size;
        }

        return self::DEFAULT_SIZE;
    }

    public function isCircular(): bool
    {
        return (bool) $this->evaluate($this->isCircular);
    }

    /**
     * Without a URL there is nothing to show, so the image counts as
     * hidden and the slot skips it.
     */
    public function isHidden(): bool
    {
        return parent::isHidden() || blank($this->getImage());
    }
}
