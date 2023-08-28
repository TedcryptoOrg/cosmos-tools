<?php

namespace App\Service\Tools;

use App\Entity\Export\Delegation;
use App\Entity\Export\Validator;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Service\Cosmos\CosmosClientFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Fetch delegations and save it into the db in a very raw and memory friendly way.
 */
class DelegationFetcherManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly CosmosClientFactory $cosmosClientFactory, private readonly LoggerInterface $logger)
    {
    }

    public function fetch(ExportDelegationsRequest $exportDelegationsRequest, Validator $validator): void
    {
        $cosmosClient = $this->cosmosClientFactory->createClientManually($exportDelegationsRequest->getApiClient());

        $page = 1;
        $offset = 0;
        $limit = 1000;
        $lastDelegator = null;
        while (true) {
            $this->logger->info('Fetching delegations for validator: '.$validator->getValidatorAddress().' page: '.$page);
            $delegations = $cosmosClient->getValidatorDelegations(
                $validator->getValidatorAddress(),
                (string) $exportDelegationsRequest->getHeight(),
                $limit,
                $offset
            );
            if ([] === $delegations->getDelegationResponses()) {
                $this->logger->info('No delegations for validator: '.$validator->getValidatorAddress());
                break;
            }
            if ($delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress() === $lastDelegator) {
                $this->logger->info('No more delegations for validator: '.$validator->getValidatorAddress());
                break;
            }
            $lastDelegator = $delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress();

            $this->entityManager->wrapInTransaction(function () use ($validator, $delegations) {
                foreach ($delegations->getDelegationResponses() as $delegation) {
                    $exportDelegation = new Delegation();
                    $exportDelegation
                        ->setDelegatorAddress($delegation->getDelegation()->getDelegatorAddress())
                        ->setShares($delegation->getBalance()->getAmount())
                    ;

                    $validator->addDelegation($exportDelegation);
                }
            });

            if (\count($delegations->getDelegationResponses()) < $limit) {
                // We got to the end of our pagination - it seems
                break;
            }

            $offset += $limit;
            ++$page;
        }
    }
}
