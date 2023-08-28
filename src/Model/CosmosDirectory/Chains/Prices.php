<?php

namespace App\Model\CosmosDirectory\Chains;

class Prices
{
    private ?array $coinGecko = null;

    public function getCoinGecko(): ?array
    {
        return $this->coinGecko;
    }

    public function setCoinGecko(?array $coinGecko): Prices
    {
        $this->coinGecko = $coinGecko;

        return $this;
    }
}
