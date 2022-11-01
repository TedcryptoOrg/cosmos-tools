<?php

namespace App\Model\CosmosDirectory\Chains\Parameters;

class DistributionParameters
{
    private string $communityTax;

    private string $baseProposerReward;

    private string $bonusProposerReward;

    private bool $withdrawAddrEnabled;

    public function getCommunityTax(): string
    {
        return $this->communityTax;
    }

    public function setCommunityTax(string $communityTax): DistributionParameters
    {
        $this->communityTax = $communityTax;
        return $this;
    }

    public function getBaseProposerReward(): string
    {
        return $this->baseProposerReward;
    }

    public function setBaseProposerReward(string $baseProposerReward): DistributionParameters
    {
        $this->baseProposerReward = $baseProposerReward;
        return $this;
    }

    public function getBonusProposerReward(): string
    {
        return $this->bonusProposerReward;
    }

    public function setBonusProposerReward(string $bonusProposerReward): DistributionParameters
    {
        $this->bonusProposerReward = $bonusProposerReward;
        return $this;
    }

    public function isWithdrawAddrEnabled(): bool
    {
        return $this->withdrawAddrEnabled;
    }

    public function setWithdrawAddrEnabled(bool $withdrawAddrEnabled): DistributionParameters
    {
        $this->withdrawAddrEnabled = $withdrawAddrEnabled;
        return $this;
    }
}