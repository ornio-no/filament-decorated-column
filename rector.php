<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withSkip([
        // Ported without strict_types; kept diffable against the original source.
        SafeDeclareStrictTypesRector::class => [
            __DIR__ . '/src/DecoratedColumn.php',
            __DIR__ . '/src/DecoratedEntry.php',
        ],
    ]);
