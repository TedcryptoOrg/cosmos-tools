<?php

namespace App\Model\CosmosDirectory\Chains;

class ServiceServer
{
    private string $address;

    private ?string $provider = null;

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): ServiceServer
    {
        $this->address = $address;
        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): ServiceServer
    {
        $this->provider = $provider;
        return $this;
    }
}