<?php

namespace App\Model\Polkachu;

use JMS\Serializer\Annotation as Serializer;

class CosmosUpgrades
{
    /**
     * @Serializer\Type("array<App\Model\Polkachu\CosmosUpgrade>")
     *
     * @Serializer\Inline()
     */
    private array $upgrades;

    /**
     * @return CosmosUpgrade[]
     */
    public function getUpgrades(): array
    {
        return $this->upgrades;
    }

    public function setUpgrades(array $upgrades): void
    {
        $this->upgrades = $upgrades;
    }
}
