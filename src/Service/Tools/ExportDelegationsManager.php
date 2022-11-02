<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Message\Tools\ExportDelegationMessage;
use App\Model\Cosmos\Staking\DelegationResponses;
use App\Service\Cosmos\CosmosClient;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\CosmosDirectory\ValidatorCosmosDirectoryClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\MessageBusInterface;

class ExportDelegationsManager
{
    private EntityManagerInterface $entityManager;

    private ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient;

    private CosmosClientFactory $cosmosClientFactory;

    private Filesystem $filesystem;

    private LoggerInterface $logger;

    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $entityManager, ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient, CosmosClientFactory $cosmosClientFactory, LoggerInterface $logger, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->validatorCosmosDirectoryClient = $validatorCosmosDirectoryClient;
        $this->cosmosClientFactory = $cosmosClientFactory;
        $this->filesystem = new Filesystem();
        $this->logger = $logger;
        $this->bus = $bus;
    }

    public function find(int $id): ?ExportDelegationsRequest
    {
        return $this->entityManager->getRepository(ExportDelegationsRequest::class)->find($id);
    }

    public function findOnePendingRequest(): ?ExportDelegationsRequest
    {
        return $this->entityManager->getRepository(ExportDelegationsRequest::class)->findOneBy(['status' => ExportStatusEnum::PENDING]);
    }

    public function createRequest(array $formData): ExportDelegationsRequest
    {
        $exportDelegationsRequest = new ExportDelegationsRequest();
        $exportDelegationsRequest
            ->setApiClient($formData['custom_api_server'] ?: $formData['api_client'])
            ->setEmail($formData['email'] ?: null)
            ->setHeight($formData['height'] ?: null)
            ->setNetwork($formData['network'])
        ;

        $this->entityManager->persist($exportDelegationsRequest);
        $this->entityManager->flush();

        return $exportDelegationsRequest;
    }

    public function flagProcessing(ExportDelegationsRequest $exportDelegationsRequest, string $status): void
    {
        $exportDelegationsRequest
            ->setStatus($status)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function flagAsDone(ExportDelegationsRequest $exportDelegationsRequest, string $downloadUrl): void
    {
        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::DONE)
            ->setDownloadLink($downloadUrl)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function flagAsErrored(ExportDelegationsRequest $exportDelegationsRequest, string $error): void
    {
        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::ERROR)
            ->setError($error)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function exportDelegations(ExportDelegationsRequest $exportDelegationsRequest): string
    {
        if ($this->filesystem->exists('var/export/'.$exportDelegationsRequest->getId())) {
            $this->logger->debug('Removing old export directory');
            $this->filesystem->remove('var/export'.$exportDelegationsRequest->getId());
        }
        $this->logger->debug('Creating export directory');
        $this->filesystem->mkdir('var/export'.$exportDelegationsRequest->getId());

        $validators = $this->validatorCosmosDirectoryClient->getChain($exportDelegationsRequest->getNetwork());
        $this->logger->info('Found '.count($validators).' validators for network: '. $exportDelegationsRequest->getNetwork());

        if ($exportDelegationsRequest->getApiClient()) {
            $this->logger->debug('Using manual api client: '.$exportDelegationsRequest->getApiClient());
            $cosmosClient = $this->cosmosClientFactory->createClientManually($exportDelegationsRequest->getApiClient());
        } else {
            $cosmosClient = $this->cosmosClientFactory->createClient($exportDelegationsRequest->getNetwork());
        }

        $limit = 1000;
        foreach ($validators as $validator) {
            $page = 1;
            $offset = 0;
            $lastDelegator = null;
            while (true) {
                $this->logger->debug('Fetching delegations for validator: '.$validator['address'].' page: '.$page);
                $delegations = $cosmosClient->getValidatorDelegations($validator['address'], (string) $exportDelegationsRequest->getHeight(), $limit, $offset);
                if (\count($delegations->getDelegationResponses()) === 0) {
                    $this->logger->debug('No delegations for validator: '.$validator['address']);
                    break;
                }
                if ($delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress() === $lastDelegator) {
                    $this->logger->debug('No more delegations for validator: '.$validator['address']);
                    break;
                }
                $lastDelegator = $delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress();

                $this->exportToCsv($exportDelegationsRequest, $delegations, $validator['address'], $page);

                if (\count($delegations->getDelegationResponses()) < $limit) {
                    // We got to the end of our pagination - it seems
                    break;
                }

                $offset += $limit;
                $page++;
            }
        }

        $this->logger->debug('Export completed');
        return 'var/export/'.$exportDelegationsRequest->getId().'/';
    }

    private function exportToCsv(ExportDelegationsRequest $exportDelegationsRequest, DelegationResponses $delegations, string $validatorAddress, int $page): void
    {
        $directoryPath = 'var/export/'.$exportDelegationsRequest->getId().'/'.$validatorAddress;
        if (!$this->filesystem->exists($directoryPath)) {
            $this->filesystem->mkdir($directoryPath);
        }

        $filePath = $directoryPath.'/delegations-'.$page.'.csv';
        $file = fopen($filePath, 'w');
        fputcsv($file, ['delegator_address', 'validator_address', 'shares', 'balance']);
        foreach ($delegations->getDelegationResponses() as $delegation) {
            fputcsv($file, [
                $delegation->getDelegation()->getDelegatorAddress(),
                $delegation->getDelegation()->getValidatorAddress(),
                $delegation->getBalance()->getAmount(),
                $delegation->getBalance()->getDenom(),
            ]);
        }
        fclose($file);
    }

    public function cancel(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::CANCELLED)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function retry(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        if ($exportDelegationsRequest->getStatus() !== ExportStatusEnum::ERROR) {
            throw new \LogicException(sprintf('Cannot retry export delegations request "%s" with status: %s', $exportDelegationsRequest->getId(), $exportDelegationsRequest->getStatus()));
        }

        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::PENDING)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();

        $this->bus->dispatch(new ExportDelegationMessage($exportDelegationsRequest->getId()));
    }
}