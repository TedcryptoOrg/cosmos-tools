<?php

namespace App\Model\CosmosDirectory\Chains;

use JMS\Serializer\Annotation as Serializer;

class Chain
{
    private string $name;

    private string $path;

    private string $chainName;

    private string $networkType;

    private string $prettyName;

    private string $chainId;

    private string $status;

    private string $bech32Prefix;

    private string $symbol;

    private string $display;

    private string $denom;

    private int $decimals;

    private string $coingeckoId;

    private string $image;

    private ?int $height = null;

    private CollectionServices $bestApis;

    private ProxyStatus $proxyStatus;

    private Versions $versions;

    /**
     * @var Explorer[]
     *
     * @Serializer\Type("array<App\Model\CosmosDirectory\Chains\Explorer>")
     */
    private array $explorers;

    /**
     * @var Prices[]
     *
     * @Serializer\Type("array<App\Model\CosmosDirectory\Chains\Prices>")
     */
    private array $prices;

    // TODO
    //private array $assets;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Chain
    {
        $this->name = $name;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Chain
    {
        $this->path = $path;
        return $this;
    }

    public function getChainName(): string
    {
        return $this->chainName;
    }

    public function setChainName(string $chainName): Chain
    {
        $this->chainName = $chainName;
        return $this;
    }

    public function getNetworkType(): string
    {
        return $this->networkType;
    }

    public function setNetworkType(string $networkType): Chain
    {
        $this->networkType = $networkType;
        return $this;
    }

    public function getPrettyName(): string
    {
        return $this->prettyName;
    }

    public function setPrettyName(string $prettyName): Chain
    {
        $this->prettyName = $prettyName;
        return $this;
    }

    public function getChainId(): string
    {
        return $this->chainId;
    }

    public function setChainId(string $chainId): Chain
    {
        $this->chainId = $chainId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Chain
    {
        $this->status = $status;
        return $this;
    }

    public function getBech32Prefix(): string
    {
        return $this->bech32Prefix;
    }

    public function setBech32Prefix(string $bech32Prefix): Chain
    {
        $this->bech32Prefix = $bech32Prefix;
        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): Chain
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function setDisplay(string $display): Chain
    {
        $this->display = $display;
        return $this;
    }

    public function getDenom(): string
    {
        return $this->denom;
    }

    public function setDenom(string $denom): Chain
    {
        $this->denom = $denom;
        return $this;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): Chain
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function getCoingeckoId(): string
    {
        return $this->coingeckoId;
    }

    public function setCoingeckoId(string $coingeckoId): Chain
    {
        $this->coingeckoId = $coingeckoId;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): Chain
    {
        $this->image = $image;
        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): Chain
    {
        $this->height = $height;
        return $this;
    }

    public function getBestApis(): CollectionServices
    {
        return $this->bestApis;
    }

    public function setBestApis(CollectionServices $bestApis): Chain
    {
        $this->bestApis = $bestApis;
        return $this;
    }

    public function getProxyStatus(): ProxyStatus
    {
        return $this->proxyStatus;
    }

    public function setProxyStatus(ProxyStatus $proxyStatus): Chain
    {
        $this->proxyStatus = $proxyStatus;
        return $this;
    }

    public function getVersions(): Versions
    {
        return $this->versions;
    }

    public function setVersions(Versions $versions): Chain
    {
        $this->versions = $versions;
        return $this;
    }

    public function getExplorers(): array
    {
        return $this->explorers;
    }

    public function setExplorers(array $explorers): Chain
    {
        $this->explorers = $explorers;
        return $this;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function setPrices(array $prices): Chain
    {
        $this->prices = $prices;
        return $this;
    }
}