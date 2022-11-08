<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\Export\ExportProcessManager;
use App\Utils\MemoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Fetch delegations and save it into the db in a very raw and memory friendly way
 */
class DelegationFetcherManager
{
    private EntityManagerInterface $entityManager;

    private ExportProcessManager $exportProcessManager;

    private LoggerInterface $logger;

    private CosmosClientFactory $cosmosClientFactory;

    public function __construct(EntityManagerInterface $entityManager, ExportProcessManager $exportProcessManager, CosmosClientFactory $cosmosClientFactory, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->exportProcessManager = $exportProcessManager;
        $this->cosmosClientFactory = $cosmosClientFactory;
        $this->logger = $logger;
    }

    public function fetch(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        if (!$export = $exportDelegationsRequest->getExportProcess()) {
            $export = $this->exportProcessManager->create($exportDelegationsRequest);
        }
        $cosmosClient = $this->cosmosClientFactory->createClientManually($exportDelegationsRequest->getApiClient());

        $limit = 1000;
        $connection = $this->entityManager->getConnection();
        $statement = $connection->executeQuery('SELECT * FROM validator WHERE export_id = :exportId', ['exportId' => $export->getId()]);
        while ($validator = $statement->fetchAssociative()) {
            if ($validator['is_completed']) {
                $this->logger->info(sprintf('Validator %s already completed', $validator['validator_address']));
                continue;
            }

            // Remove previous one if any
            if ($connection->executeQuery('SELECT COUNT(*) FROM delegation WHERE validator_id = :validatorId', ['validatorId' => $validator['id']])->fetchOne()) {
                $this->logger->info(sprintf('Removing previous delegations for validator %s', $validator['validator_address']));
                $connection->executeQuery('DELETE FROM delegation WHERE validator_id = :validatorId', ['validatorId' => $validator['id']]);
            }

            $page = 1;
            $offset = 0;
            $lastDelegator = null;
            while (true) {
                $this->logger->info('Fetching delegations for validator: '.$validator['validator_address'].' page: '.$page);
                $delegations = $cosmosClient->getValidatorDelegations($validator['validator_address'], $export->getHeight(), $limit, $offset);
                if (\count($delegations->getDelegationResponses()) === 0) {
                    $this->logger->info('No delegations for validator: '.$validator['validator_address']);
                    break;
                }
                if ($delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress() === $lastDelegator) {
                    $this->logger->info('No more delegations for validator: '.$validator['validator_address']);
                    break;
                }
                $lastDelegator = $delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress();

                foreach ($delegations->getDelegationResponses() as $delegation) {
                    $connection->executeQuery('
                        INSERT INTO delegation (validator_id, delegator_address, shares) 
                        VALUES (:validatorId, :delegatorAddress, :shares)', [
                            'validatorId' => $validator['id'],
                            'delegatorAddress' => $delegation->getDelegation()->getDelegatorAddress(),
                            'shares' => $delegation->getBalance()->getAmount(),
                        ]
                    );
                }

                if (\count($delegations->getDelegationResponses()) < $limit) {
                    // We got to the end of our pagination - it seems
                    break;
                }

                $offset += $limit;
                $page++;
            }

            $this->logger->info(sprintf('Validator %s completed', $validator['validator_address']));
            $connection->executeQuery('UPDATE validator SET is_completed = 1, completed_at = NOW() WHERE id = :id', ['id' => $validator['id']]);

            MemoryUtil::printMemoryUsage();
        }

        $this->logger->info('Export completed');
    }
}