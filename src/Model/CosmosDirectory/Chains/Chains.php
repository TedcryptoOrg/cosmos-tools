<?php

namespace App\Model\CosmosDirectory\Chains;

use JMS\Serializer\Annotation as Serializer;

class Chains
{
    private Repository $repository;

    /**
     * @Serializer\Type("array<App\Model\CosmosDirectory\Chains\Chain>")
     *
     * @var array<Chain>
     */
    private array $chains;

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function setRepository(Repository $repository): Chains
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return array<Chain>
     */
    public function getChains(): array
    {
        return $this->chains;
    }

    /**
     * @param array<Chain> $chains
     */
    public function setChains(array $chains): Chains
    {
        $this->chains = $chains;

        return $this;
    }
}
