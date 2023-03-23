<?php

namespace App\Model\Polkachu;

use App\Model\CosmosDirectory\Chains\Chain;
use App\Model\CosmosDirectory\Chains\Repository;
use JMS\Serializer\Annotation as Serializer;

class CosmosUpgrades
{
    private Repository $repository;

    /**
     * @Serializer\Type("array<App\Model\Polkachu\CosmosUpgrade>")
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