<?php

namespace App\Application\UseCase\ListGrants;

use App\Model\Cosmos\Authz\GranterGrantsResponse;
use App\Service\Cosmos\CosmosClientFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ListGrantCommandHandler
{
    public function __construct(
        private readonly CosmosClientFactory $cosmosClientFactory
    ) { }

    public function __invoke(ListGrantCommand $listGrantCommand): GranterGrantsResponse
   {
        $authzClient = $this->cosmosClientFactory->createAuthzClient();

        return $authzClient->getListGrantsByGranter($listGrantCommand->granter);
   }
}