<?php

namespace App\Infrastructure\UpgradeWatcher;

use App\Infrastructure\UpgradeWatcher\Adapter\DefiantLabs\ChainUpgradeData;
use App\Infrastructure\UpgradeWatcher\Adapter\DefiantLabs\UpgradeApiClient;
use App\Model\UpgradeWatcher\ChainUpgrade;
use App\Model\UpgradeWatcher\UpgradeWatcher;

class UpgradeWatcherEngine implements UpgradeWatcher
{
    public function __construct(private readonly UpgradeApiClient $upgradeApiClient)
    {
    }

    public function getUpgrades(): array
    {
        return \array_map(
            fn (ChainUpgradeData $data) => new ChainUpgrade(
                $data->network,
                $data->network,
                $data->upgradeName,
                $data->estimatedUpgradeTime,
                $data->upgradeBlockHeight,
            ),
            $this->upgradeApiClient->getCosmosUpgrades()
        );
    }
}
