<?php

namespace App\Tests\Integration\Service\Cosmos;

use App\Service\Cosmos\CosmosClientFactory;
use App\Tests\Integration\BaseIntegrationTestCase;

class CosmosClientTest extends BaseIntegrationTestCase
{
    public function testGetDelegations(): void
    {
        /** @var CosmosClientFactory $cosmosClientFactory */
        $cosmosClientFactory = $this->getService(CosmosClientFactory::class);
        $client = $cosmosClientFactory->createClient('secretnetwork');

        $delegations = $client->getValidatorDelegations('secretvaloper10wxn2lv29yqnw2uf4jf439kwy5ef00qdrjxpjn', 1);
        $this->assertCount(1, $delegations->getDelegationResponses());
    }
}
