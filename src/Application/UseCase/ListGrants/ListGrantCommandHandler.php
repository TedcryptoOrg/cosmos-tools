<?php

namespace App\Application\UseCase\ListGrants;

use App\Model\Cosmos\Authz\GranterGrantsResponse;
use App\Service\Cosmos\CosmosClientFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

#[AsMessageHandler]
class ListGrantCommandHandler
{
    public function __construct(
        private readonly CosmosClientFactory $cosmosClientFactory
    ) { }

    public function __invoke(ListGrantCommand $listGrantCommand): GranterGrantsResponse
   {
        $decoded = Bech32::decode($listGrantCommand->granter);
        $prefixToChain = [
            'cosmos' => 'cosmoshub',
            'osmo' => 'osmosis',
        ];
        $authzClient = $this->cosmosClientFactory->createAuthzClient($prefixToChain[$decoded[0]] ?? $decoded[0]);

        return $authzClient->getListGrantsByGranter($listGrantCommand->granter);
   }
}