<?php

namespace App\Tests\Integration\Service\CosmosDirectory;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use App\Tests\Integration\BaseIntegrationTestCase;

class CosmosDirectoryClientTest extends BaseIntegrationTestCase
{
    private ChainsCosmosDirectoryClient $client;

    protected function setUp(): void
    {
        /** @var ChainsCosmosDirectoryClient $client */
        $client = $this->getService(ChainsCosmosDirectoryClient::class);

        $this->client = $client;
    }

    public function testGetAllChains(): void
    {
        $chains = $this->client->getAllChains();
        self::assertCount(83, $chains->getChains());
        self::assertSame('agoric', $chains->getChains()[0]->getChainName());
    }

    public function testGetChain(): void
    {
        $chain = $this->client->getChain('cosmoshub');
        self::assertSame('cosmoshub', $chain['chain_name']);
        self::assertSame('gaiad', $chain['daemon_name']);
    }
}
