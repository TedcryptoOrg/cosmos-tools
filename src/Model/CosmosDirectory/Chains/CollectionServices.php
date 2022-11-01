<?php

namespace App\Model\CosmosDirectory\Chains;

use JMS\Serializer\Annotation as Serializer;

class CollectionServices
{
    /**
     * @var ServiceServer[]
     *
     * @Serializer\Type("array<App\Model\CosmosDirectory\Chains\ServiceServer>")
     */
    private ?array $rest = null;

    /**
     * @var ServiceServer[]
     *
     * @Serializer\Type("array<App\Model\CosmosDirectory\Chains\ServiceServer>")
     */
    private ?array $rpc = null;

    /**
     * @return ServiceServer[]|null
     */
    public function getRest(): ?array
    {
        return $this->rest;
    }

    public function setRest(?array $rest): CollectionServices
    {
        $this->rest = $rest;
        return $this;
    }

    /**
     * @return ServiceServer[]|null
     */
    public function getRpc(): ?array
    {
        return $this->rpc;
    }

    public function setRpc(?array $rpc): CollectionServices
    {
        $this->rpc = $rpc;
        return $this;
    }
}