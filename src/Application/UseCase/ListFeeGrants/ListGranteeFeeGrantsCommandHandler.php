<?php

namespace App\Application\UseCase\ListFeeGrants;

use App\Model\Cosmos\FeeGrant\GranteeFeeGrantsResponse;
use App\Service\Cosmos\CosmosClientFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

#[AsMessageHandler]
class ListGranteeFeeGrantsCommandHandler
{
    public function __construct(
        private readonly CosmosClientFactory $cosmosClientFactory
    ) {
    }

    public function __invoke(ListGranteeFeeGrantsCommand $listGrantCommand): GranteeFeeGrantsResponse
    {
        $decoded = Bech32::decode($listGrantCommand->grantee);
        $prefixToChain = [
            'cosmos' => 'cosmoshub',
            'osmo' => 'osmosis',
        ];
        $authzClient = $this->cosmosClientFactory->createFeeGrantClient($prefixToChain[$decoded[0]] ?? $decoded[0]);

        return $authzClient->getFeeGrantsByGrantee($listGrantCommand->grantee);
    }
}
