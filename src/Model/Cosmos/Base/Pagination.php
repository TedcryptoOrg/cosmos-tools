<?php

namespace App\Model\Cosmos\Base;

class Pagination
{
    private ?string $nextKey = null;

    private string $total;

    public function getNextKey(): ?string
    {
        return $this->nextKey;
    }

    public function setNextKey(?string $nextKey): void
    {
        $this->nextKey = $nextKey;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): void
    {
        $this->total = $total;
    }
}
