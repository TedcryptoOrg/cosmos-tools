<?php

namespace App\Service\Cosmos;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;

class CosmosClientFactory
{
    public function __construct(private readonly ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient, private readonly LoggerInterface $logger)
    {
    }

    public function createClient(string $chain): CosmosClient
    {
        $chainDirectory = $this->chainsCosmosDirectoryClient->getChain($chain);
        $servers = $chainDirectory['best_apis']['rest'];
        $provider = $servers[random_int(0, (is_countable($servers) ? \count($servers) : 0) - 1)];

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
