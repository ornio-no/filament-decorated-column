<?php

namespace Ornio\FilamentDecoratedColumn\Commands;

use Illuminate\Console\Command;

class FilamentDecoratedColumnCommand extends Command
{
    public $signature = 'filament-decorated-column';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
