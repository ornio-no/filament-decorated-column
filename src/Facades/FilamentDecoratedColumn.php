<?php

namespace Ornio\FilamentDecoratedColumn\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ornio\FilamentDecoratedColumn\FilamentDecoratedColumn
 */
class FilamentDecoratedColumn extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ornio\FilamentDecoratedColumn\FilamentDecoratedColumn::class;
    }
}
