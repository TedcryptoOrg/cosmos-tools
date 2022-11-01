<?php

namespace App\Model\CosmosDirectory\Chains;

class ProxyStatus
{
    private bool $rest;

    private bool $rpc;

    public function isRest(): bool
    {
        return $this->rest;
    }

    public function setRest(bool $rest): ProxyStatus
    {
        $this->rest = $rest;
        return $this;
    }

    public function isRpc(): bool
    {
        return $this->rpc;
    }

    public function setRpc(bool $rpc): ProxyStatus
    {
        $this->rpc = $rpc;
        return $this;
    }
}