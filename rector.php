<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;

return static function (RectorConfig $rector): void {
    $rector->parallel();
    $rector->paths([__DIR__]);
    $rector->phpVersion(PhpVersion::PHP_82);
    $rector->phpstanConfig(__DIR__.'/phpstan.neon');
    $rector->skip([
        __DIR__.'/var',
        __DIR__.'/vendor',
        FlipTypeControlToUseExclusiveTypeRector::class,
    ]);
    $rector->sets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
        SymfonyLevelSetList::UP_TO_SYMFONY_63,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
    ]);
};
