<?php

namespace Ornio\FilamentDecoratedColumn;

use Filament\Tables\Columns\TextColumn;
use Ornio\FilamentDecoratedColumn\Concerns\HasDecorations;

/**
 * A TextColumn with Badge and Icon decorations before or after its
 * text. See HasDecorations for the API.
 */
class DecoratedColumn extends TextColumn
{
    use HasDecorations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(static fn (mixed $state): mixed => $state);
    }
}
