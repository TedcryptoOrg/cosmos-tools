<?php

namespace App\Model\CosmosDirectory\Chains;

class Repository
{
    private string $url;

    private string $branch;

    private string $commit;

    private string $timestamp;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Repository
    {
        $this->url = $url;
        return $this;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function setBranch(string $branch): Repository
    {
        $this->branch = $branch;
        return $this;
    }

    public function getCommit(): string
    {
        return $this->commit;
    }

    public function setCommit(string $commit): Repository
    {
        $this->commit = $commit;
        return $this;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): Repository
    {
        $this->timestamp = $timestamp;
        return $this;
    }


}