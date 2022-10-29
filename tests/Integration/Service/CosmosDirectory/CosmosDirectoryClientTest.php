<?php

namespace App\Tests\Integration\Service\CosmosDirectory;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use App\Tests\Integration\BaseIntegrationTestCase;
use PHPUnit\Framework\TestCase;

class CosmosDirectoryClientTest extends BaseIntegrationTestCase
{
    private ?ChainsCosmosDirectoryClient $client;

    protected function setUp(): void
    {
        $this->client = $this->getService(ChainsCosmosDirectoryClient::class);
    }

    public function testGetAllChains(): void
    {
        $chains = $this->client->getAllChains();
        $this->assertCount(83, $chains);
        $this->assertSame('agoric', $chains[0]['name']);
    }

    public function testGetChain(): void
    {
        $chain = $this->client->getChain('cosmoshub');
        $this->assertSame('cosmoshub', $chain['chain_name']);
        $this->assertSame('gaiad', $chain['daemon_name']);
    }
}
