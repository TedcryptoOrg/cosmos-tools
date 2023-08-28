<?php

namespace App\Model\Cosmos\Staking;

class Balance
{
    private string $denom;

    private string $amount;

    public function getDenom(): string
    {
        return $this->denom;
    }

    public function setDenom(string $denom): void
    {
        $this->denom = $denom;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }
}
