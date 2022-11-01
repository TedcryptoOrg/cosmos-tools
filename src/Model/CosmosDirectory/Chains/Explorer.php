<?php

namespace App\Model\CosmosDirectory\Chains;

class Explorer
{
    private ?string $kind = null;

    private ?string $name = null;

    private string $url;

    private string $txPage;

    private string $accountPage;

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind): Explorer
    {
        $this->kind = $kind;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Explorer
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Explorer
    {
        $this->url = $url;
        return $this;
    }

    public function getTxPage(): string
    {
        return $this->txPage;
    }

    public function setTxPage(string $txPage): Explorer
    {
        $this->txPage = $txPage;
        return $this;
    }

    public function getAccountPage(): string
    {
        return $this->accountPage;
    }

    public function setAccountPage(string $accountPage): Explorer
    {
        $this->accountPage = $accountPage;
        return $this;
    }
}