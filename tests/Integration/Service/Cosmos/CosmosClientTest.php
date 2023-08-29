<?php

namespace App\Tests\Integration\Service\Cosmos;

use App\Service\Cosmos\CosmosClientFactory;
use App\Tests\Integration\BaseIntegrationTestCase;

class CosmosClientTest extends BaseIntegrationTestCase
{
    public function testGetDelegations(): void
    {
        $this->markTestSkipped('Problems with performance with public endpoints');
        /** @var CosmosClientFactory $cosmosClientFactory */
        $cosmosClientFactory = $this->getService(CosmosClientFactory::class);
        $client = $cosmosClientFactory->createClient('osmosis');

        $delegations = $client->getValidatorDelegations('osmovaloper1xk23a255qm4kn6gdezr6jm7zmupn23t3pqjjn6', '11211113');
        self::assertCount(1, $delegations->getDelegationResponses());
    }
}
