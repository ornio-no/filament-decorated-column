<?php

declare(strict_types=1);

namespace Ornio\FilamentDecoratedColumn;

use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasWeight;

/**
 * Secondary text beside the column text, such as "(you)" or "#1042": the
 * decoration's label, escaped. Gray unless given a palette colour, with
 * optional weight and font family, and no size of its own: it takes the
 * size of the text around it.
 */
class TextDecoration extends Decoration
{
    use HasFontFamily;
    use HasWeight;

    protected string $view = 'filament-decorated-column::text';
}
