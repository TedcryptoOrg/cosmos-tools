<?php

namespace App\Service\Cosmos;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;

class CosmosClientFactory
{
    private ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient;

    public function __construct(ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient)
    {
        $this->chainsCosmosDirectoryClient = $chainsCosmosDirectoryClient;
    }

    public function createClient(string $chain): CosmosClient
    {
        $chainDirectory = $this->chainsCosmosDirectoryClient->getChain($chain);
        $servers = $chainDirectory['best_apis']['rest'];
        $provider = $servers[\rand(0, \count($servers) - 1)];
        return new CosmosClient($provider['address'], $provider['provider'] ?? 'Unknown');
    }
}