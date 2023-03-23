<?php

declare(strict_types=1);

namespace App\Model\Polkachu;

use JMS\Serializer\Annotation as Serializer;

class CosmosUpgrade
{
    private string $network;

    private string $chainName;

    private string $repo;

    private string $nodeVersion;

    private string $cosmovisorFolder;

    private string $gitHash;

    private string $proposal;

    private int $block;

    private string $blockLink;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.u\Z'>")
     */
    private \DateTimeImmutable $estimatedUpgradeTime;

    private string $guide;

    private string $rpc;

    private string $api;

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network): CosmosUpgrade
    {
        $this->network = $network;
        return $this;
    }

    public function getChainName(): string
    {
        return $this->chainName;
    }

    public function setChainName(string $chainName): CosmosUpgrade
    {
        $this->chainName = $chainName;
        return $this;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function setRepo(string $repo): CosmosUpgrade
    {
        $this->repo = $repo;
        return $this;
    }

    public function getNodeVersion(): string
    {
        return $this->nodeVersion;
    }

    public function setNodeVersion(string $nodeVersion): CosmosUpgrade
    {
        $this->nodeVersion = $nodeVersion;
        return $this;
    }

    public function getCosmovisorFolder(): string
    {
        return $this->cosmovisorFolder;
    }

    public function setCosmovisorFolder(string $cosmovisorFolder): CosmosUpgrade
    {
        $this->cosmovisorFolder = $cosmovisorFolder;
        return $this;
    }

    public function getGitHash(): string
    {
        return $this->gitHash;
    }

    public function setGitHash(string $gitHash): CosmosUpgrade
    {
        $this->gitHash = $gitHash;
        return $this;
    }

    public function getProposal(): string
    {
        return $this->proposal;
    }

    public function setProposal(string $proposal): CosmosUpgrade
    {
        $this->proposal = $proposal;
        return $this;
    }

    public function getBlock(): int
    {
        return $this->block;
    }

    public function setBlock(int $block): CosmosUpgrade
    {
        $this->block = $block;
        return $this;
    }

    public function getBlockLink(): string
    {
        return $this->blockLink;
    }

    public function setBlockLink(string $blockLink): CosmosUpgrade
    {
        $this->blockLink = $blockLink;
        return $this;
    }

    public function getEstimatedUpgradeTime(): \DateTimeImmutable
    {
        return $this->estimatedUpgradeTime;
    }

    public function setEstimatedUpgradeTime(\DateTimeImmutable $estimatedUpgradeTime): CosmosUpgrade
    {
        $this->estimatedUpgradeTime = $estimatedUpgradeTime;
        return $this;
    }

    public function getGuide(): string
    {
        return $this->guide;
    }

    public function setGuide(string $guide): CosmosUpgrade
    {
        $this->guide = $guide;
        return $this;
    }

    public function getRpc(): string
    {
        return $this->rpc;
    }

    public function setRpc(string $rpc): CosmosUpgrade
    {
        $this->rpc = $rpc;
        return $this;
    }

    public function getApi(): string
    {
        return $this->api;
    }

    public function setAp(string $api): CosmosUpgrade
    {
        $this->api = $api;
        return $this;
    }
}