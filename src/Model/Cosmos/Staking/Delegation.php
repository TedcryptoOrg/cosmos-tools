<?php

namespace App\Model\Cosmos\Staking;

class Delegation
{
    private string $delegatorAddress;

    private string $validatorAddress;

    private string $shares;

    public function getDelegatorAddress(): string
    {
        return $this->delegatorAddress;
    }

    public function setDelegatorAddress(string $delegatorAddress): void
    {
        $this->delegatorAddress = $delegatorAddress;
    }

    public function getValidatorAddress(): string
    {
        return $this->validatorAddress;
    }

    public function setValidatorAddress(string $validatorAddress): void
    {
        $this->validatorAddress = $validatorAddress;
    }

    public function getShares(): string
    {
        return $this->shares;
    }

    public function setShares(string $shares): void
    {
        $this->shares = $shares;
    }
}
