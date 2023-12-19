<?php

namespace App\Model\UpgradeWatcher;

interface UpgradeWatcher
{
    /**
     * @return array<ChainUpgrade>
     */
    public function getUpgrades(): array;
}
