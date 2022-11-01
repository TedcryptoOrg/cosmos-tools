<?php

namespace App\Model\CosmosDirectory\Chains\Parameters;

class MintParameters
{
    private string $mintDenom;

    private string $inflationRateChange;

    private string $inflationMax;

    private string $inflationMin;

    private string $goalBonded;

    private string $blocksPerYear;

    public function getMintDenom(): string
    {
        return $this->mintDenom;
    }

    public function setMintDenom(string $mintDenom): MintParameters
    {
        $this->mintDenom = $mintDenom;
        return $this;
    }

    public function getInflationRateChange(): string
    {
        return $this->inflationRateChange;
    }

    public function setInflationRateChange(string $inflationRateChange): MintParameters
    {
        $this->inflationRateChange = $inflationRateChange;
        return $this;
    }

    public function getInflationMax(): string
    {
        return $this->inflationMax;
    }

    public function setInflationMax(string $inflationMax): MintParameters
    {
        $this->inflationMax = $inflationMax;
        return $this;
    }

    public function getInflationMin(): string
    {
        return $this->inflationMin;
    }

    public function setInflationMin(string $inflationMin): MintParameters
    {
        $this->inflationMin = $inflationMin;
        return $this;
    }

    public function getGoalBonded(): string
    {
        return $this->goalBonded;
    }

    public function setGoalBonded(string $goalBonded): MintParameters
    {
        $this->goalBonded = $goalBonded;
        return $this;
    }

    public function getBlocksPerYear(): string
    {
        return $this->blocksPerYear;
    }

    public function setBlocksPerYear(string $blocksPerYear): MintParameters
    {
        $this->blocksPerYear = $blocksPerYear;
        return $this;
    }
}