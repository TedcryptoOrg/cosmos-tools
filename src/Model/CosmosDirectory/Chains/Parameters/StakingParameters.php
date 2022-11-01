<?php

namespace App\Model\CosmosDirectory\Chains\Parameters;

class StakingParameters
{
    private string $unboundingTime;

    private int $maxValidators;

    private int $maxEntries;

    private int $historicalEntries;

    private string $bondDenom;

    public function getUnboundingTime(): string
    {
        return $this->unboundingTime;
    }

    public function setUnboundingTime(string $unboundingTime): StakingParameters
    {
        $this->unboundingTime = $unboundingTime;
        return $this;
    }

    public function getMaxValidators(): int
    {
        return $this->maxValidators;
    }

    public function setMaxValidators(int $maxValidators): StakingParameters
    {
        $this->maxValidators = $maxValidators;
        return $this;
    }

    public function getMaxEntries(): int
    {
        return $this->maxEntries;
    }

    public function setMaxEntries(int $maxEntries): StakingParameters
    {
        $this->maxEntries = $maxEntries;
        return $this;
    }

    public function getHistoricalEntries(): int
    {
        return $this->historicalEntries;
    }

    public function setHistoricalEntries(int $historicalEntries): StakingParameters
    {
        $this->historicalEntries = $historicalEntries;
        return $this;
    }

    public function getBondDenom(): string
    {
        return $this->bondDenom;
    }

    public function setBondDenom(string $bondDenom): StakingParameters
    {
        $this->bondDenom = $bondDenom;
        return $this;
    }
}