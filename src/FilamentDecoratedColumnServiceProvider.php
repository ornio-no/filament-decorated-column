<?php

declare(strict_types=1);

namespace Ornio\FilamentDecoratedColumn;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentDecoratedColumnServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-decorated-column';

    public static string $viewNamespace = 'filament-decorated-column';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace);
    }
}
