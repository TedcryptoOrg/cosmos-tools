<?php

namespace App\Tests\Integration\Service\UpgradeWatcher\Adapter\DefiantLabs;

use App\Infrastructure\UpgradeWatcher\Adapter\DefiantLabs\UpgradeApiClient;
use App\Tests\Integration\BaseIntegrationTestCase;

class UpgradeApiClientTest extends BaseIntegrationTestCase
{
    private UpgradeApiClient $client;

    protected function setUp(): void
    {
        $this->client = self::getService(UpgradeApiClient::class);
    }

    public function testGetCosmosUpgrades(): void
    {
        $upgrades = $this->client->getCosmosUpgrades();

        self::assertNotEmpty($upgrades);
    }
}
