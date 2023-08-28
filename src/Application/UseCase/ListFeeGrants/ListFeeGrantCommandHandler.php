<?php

namespace App\Application\UseCase\ListFeeGrants;

use App\Model\Cosmos\FeeGrant\GranterFeeGrantsResponse;
use App\Service\Cosmos\CosmosClientFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

#[AsMessageHandler]
class ListFeeGrantCommandHandler
{
    public function __construct(
        private readonly CosmosClientFactory $cosmosClientFactory
    ) { }

    public function __invoke(ListFeeGrantCommand $listGrantCommand): GranterFeeGrantsResponse
   {
        $decoded = Bech32::decode($listGrantCommand->granter);
        $prefixToChain = [
            'cosmos' => 'cosmoshub',
            'osmo' => 'osmosis',
        ];
        $authzClient = $this->cosmosClientFactory->createFeeGrantClient($prefixToChain[$decoded[0]] ?? $decoded[0]);

        return $authzClient->getFeeGrantsByGranter($listGrantCommand->granter);
   }
}