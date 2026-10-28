<?php

declare(strict_types=1);

namespace Ornio\FilamentDecoratedColumn;

/**
 * A small filled circle in a palette colour that signals a status, gray
 * by default. It has a fixed size, so there is no size() setter.
 */
class DotDecoration extends Decoration
{
    protected string $view = 'filament-decorated-column::dot';
}
