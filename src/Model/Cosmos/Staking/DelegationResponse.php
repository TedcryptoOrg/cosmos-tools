<?php

namespace App\Model\Cosmos\Staking;

class DelegationResponse
{
    private Delegation $delegation;

    private Balance $balance;

    public function getDelegation(): Delegation
    {
        return $this->delegation;
    }

    public function setDelegation(Delegation $delegation): void
    {
        $this->delegation = $delegation;
    }

    public function getBalance(): Balance
    {
        return $this->balance;
    }

    public function setBalance(Balance $balance): void
    {
        $this->balance = $balance;
    }
}
