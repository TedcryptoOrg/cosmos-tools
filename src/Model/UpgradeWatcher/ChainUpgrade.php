<?php

namespace App\Model\UpgradeWatcher;

final class ChainUpgrade
{
    public function __construct(
        public string $network,
        public string $chainName,
        public string $version,
        public \DateTimeImmutable $estimatedUpgradeTime,
        public string $upgradeBlockHeight,
    ) {
    }
}
