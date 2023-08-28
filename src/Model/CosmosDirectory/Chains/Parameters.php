<?php

namespace App\Model\CosmosDirectory\Chains;

use App\Model\CosmosDirectory\Chains\Parameters\DistributionParameters;
use App\Model\CosmosDirectory\Chains\Parameters\MintParameters;
use App\Model\CosmosDirectory\Chains\Parameters\SlashingParameters;
use App\Model\CosmosDirectory\Chains\Parameters\StakingParameters;

class Parameters
{
    private bool $authz;

    private float $actualBlockTime;

    private float $actualBlocksPerYear;

    private int $unboundingTime;

    private int $maxValidators;

    private int $blocksPerYear;

    private float $blockTime;

    private int|float $communityTax;

    private float $baseInflation;

    private float $estimatedApr;

    private float $calculatedApr;

    private StakingParameters $stakingParameters;

    private SlashingParameters $slashingParameters;

    private MintParameters $mintParameters;

    private DistributionParameters $distributionParameters;

    private string $totalSupply;

    private string $annualProvision;

    private string $bondedTokens;

    private ?float $bondedRatio = null;

    private string $currentBlockHeight;

    public function isAuthz(): bool
    {
        return $this->authz;
    }

    public function setAuthz(bool $authz): Parameters
    {
        $this->authz = $authz;

        return $this;
    }

    public function getActualBlockTime(): float
    {
        return $this->actualBlockTime;
    }

    public function setActualBlockTime(float $actualBlockTime): Parameters
    {
        $this->actualBlockTime = $actualBlockTime;

        return $this;
    }

    public function getActualBlocksPerYear(): float
    {
        return $this->actualBlocksPerYear;
    }

    public function setActualBlocksPerYear(float $actualBlocksPerYear): Parameters
    {
        $this->actualBlocksPerYear = $actualBlocksPerYear;

        return $this;
    }

    public function getUnboundingTime(): int
    {
        return $this->unboundingTime;
    }

    public function setUnboundingTime(int $unboundingTime): Parameters
    {
        $this->unboundingTime = $unboundingTime;

        return $this;
    }

    public function getMaxValidators(): int
    {
        return $this->maxValidators;
    }

    public function setMaxValidators(int $maxValidators): Parameters
    {
        $this->maxValidators = $maxValidators;

        return $this;
    }

    public function getBlocksPerYear(): int
    {
        return $this->blocksPerYear;
    }

    public function setBlocksPerYear(int $blocksPerYear): Parameters
    {
        $this->blocksPerYear = $blocksPerYear;

        return $this;
    }

    public function getBlockTime(): float
    {
        return $this->blockTime;
    }

    public function setBlockTime(float $blockTime): Parameters
    {
        $this->blockTime = $blockTime;

        return $this;
    }

    public function getCommunityTax(): float|int
    {
        return $this->communityTax;
    }

    public function setCommunityTax(float|int $communityTax): Parameters
    {
        $this->communityTax = $communityTax;

        return $this;
    }

    public function getBaseInflation(): float
    {
        return $this->baseInflation;
    }

    public function setBaseInflation(float $baseInflation): Parameters
    {
        $this->baseInflation = $baseInflation;

        return $this;
    }

    public function getEstimatedApr(): float
    {
        return $this->estimatedApr;
    }

    public function setEstimatedApr(float $estimatedApr): Parameters
    {
        $this->estimatedApr = $estimatedApr;

        return $this;
    }

    public function getCalculatedApr(): float
    {
        return $this->calculatedApr;
    }

    public function setCalculatedApr(float $calculatedApr): Parameters
    {
        $this->calculatedApr = $calculatedApr;

        return $this;
    }

    public function getStakingParameters(): StakingParameters
    {
        return $this->stakingParameters;
    }

    public function setStakingParameters(StakingParameters $stakingParameters): Parameters
    {
        $this->stakingParameters = $stakingParameters;

        return $this;
    }

    public function getSlashingParameters(): SlashingParameters
    {
        return $this->slashingParameters;
    }

    public function setSlashingParameters(SlashingParameters $slashingParameters): Parameters
    {
        $this->slashingParameters = $slashingParameters;

        return $this;
    }

    public function getMintParameters(): MintParameters
    {
        return $this->mintParameters;
    }

    public function setMintParameters(MintParameters $mintParameters): Parameters
    {
        $this->mintParameters = $mintParameters;

        return $this;
    }

    public function getDistributionParameters(): DistributionParameters
    {
        return $this->distributionParameters;
    }

    public function setDistributionParameters(DistributionParameters $distributionParameters): Parameters
    {
        $this->distributionParameters = $distributionParameters;

        return $this;
    }

    public function getTotalSupply(): string
    {
        return $this->totalSupply;
    }

    public function setTotalSupply(string $totalSupply): Parameters
    {
        $this->totalSupply = $totalSupply;

        return $this;
    }

    public function getAnnualProvision(): string
    {
        return $this->annualProvision;
    }

    public function setAnnualProvision(string $annualProvision): Parameters
    {
        $this->annualProvision = $annualProvision;

        return $this;
    }

    public function getBondedTokens(): string
    {
        return $this->bondedTokens;
    }

    public function setBondedTokens(string $bondedTokens): Parameters
    {
        $this->bondedTokens = $bondedTokens;

        return $this;
    }

    public function getBondedRatio(): ?float
    {
        return $this->bondedRatio;
    }

    public function setBondedRatio(?float $bondedRatio): Parameters
    {
        $this->bondedRatio = $bondedRatio;

        return $this;
    }

    public function getCurrentBlockHeight(): string
    {
        return $this->currentBlockHeight;
    }

    public function setCurrentBlockHeight(string $currentBlockHeight): Parameters
    {
        $this->currentBlockHeight = $currentBlockHeight;

        return $this;
    }
}
