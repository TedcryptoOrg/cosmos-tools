<?php

namespace App\Service\Cosmos;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;

class CosmosClientFactory
{
    private ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient;

    private LoggerInterface $logger;

    public function __construct(ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient, LoggerInterface $logger)
    {
        $this->chainsCosmosDirectoryClient = $chainsCosmosDirectoryClient;
        $this->logger = $logger;
    }

    public function createClient(string $chain): CosmosClient
    {
        $chainDirectory = $this->chainsCosmosDirectoryClient->getChain($chain);
        $servers = $chainDirectory['best_apis']['rest'];
        $provider = $servers[\rand(0, \count($servers) - 1)];

        return new CosmosClient($provider['address'], $provider['provider'] ?? 'Unknown', $this->createSerializer(), $this->logger);
    }

    public function createClientManually(string $serverAddress): CosmosClient
    {
        return new CosmosClient($serverAddress, 'Custom', $this->createSerializer(), $this->logger);
    }

    private function createSerializer(): Serializer
    {
        return SerializerBuilder::create()->build();
    }
}