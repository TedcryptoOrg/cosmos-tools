<?php

namespace App\Model\CosmosDirectory\Chains;

class Versions
{
    private string $applicationVersion;

    private string $cosmosSdkVersion;

    private string $tendermintVersion;

    public function getApplicationVersion(): string
    {
        return $this->applicationVersion;
    }

    public function setApplicationVersion(string $applicationVersion): Versions
    {
        $this->applicationVersion = $applicationVersion;

        return $this;
    }

    public function getCosmosSdkVersion(): string
    {
        return $this->cosmosSdkVersion;
    }

    public function setCosmosSdkVersion(string $cosmosSdkVersion): Versions
    {
        $this->cosmosSdkVersion = $cosmosSdkVersion;

        return $this;
    }

    public function getTendermintVersion(): string
    {
        return $this->tendermintVersion;
    }

    public function setTendermintVersion(string $tendermintVersion): Versions
    {
        $this->tendermintVersion = $tendermintVersion;

        return $this;
    }
}
