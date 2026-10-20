<?php

namespace Ornio\FilamentDecoratedColumn;

use Filament\Infolists\Components\TextEntry;
use Ornio\FilamentDecoratedColumn\Concerns\HasDecorations;

/**
 * The infolist twin of DecoratedColumn.
 */
class DecoratedEntry extends TextEntry
{
    use HasDecorations;
}
