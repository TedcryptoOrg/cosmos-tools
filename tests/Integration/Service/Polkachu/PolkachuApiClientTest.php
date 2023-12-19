<?php

namespace App\Tests\Integration\Service\Polkachu;

use App\Service\Polkachu\PolkachuApiClient;
use App\Tests\Integration\BaseIntegrationTestCase;
use PHPUnit\Framework\TestCase;

class PolkachuApiClientTest extends BaseIntegrationTestCase
{
    private PolkachuApiClient $client;

    protected function setUp(): void
    {
        $this->client = self::getService(PolkachuApiClient::class);
    }

    public function testGetCosmosUpgrades(): void
    {
        $upgrades = $this->client->getCosmosUpgrades();

        self::assertNotEmpty($upgrades);
    }
}
