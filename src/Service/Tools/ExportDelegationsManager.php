<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Event\Tools\ExportDelegationsRequestDoneEvent;
use App\Model\Cosmos\Staking\DelegationResponses;
use App\Service\Cosmos\CosmosClient;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\CosmosDirectory\ValidatorCosmosDirectoryClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class ExportDelegationsManager
{
    private EntityManagerInterface $entityManager;

    private ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient;

    private CosmosClientFactory $cosmosClientFactory;

    private EventDispatcherInterface $eventDispatcher;

    private Filesystem $filesystem;

    public function __construct(EntityManagerInterface $entityManager, ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient, EventDispatcherInterface $eventDispatcher, CosmosClientFactory $cosmosClientFactory)
    {
        $this->entityManager = $entityManager;
        $this->validatorCosmosDirectoryClient = $validatorCosmosDirectoryClient;
        $this->cosmosClientFactory = $cosmosClientFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = new Filesystem();
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
            ->setApiClient($formData['api_client'])
            ->setEmail($formData['email'])
            ->setHeight($formData['height'])
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

        $this->eventDispatcher->dispatch(new ExportDelegationsRequestDoneEvent($exportDelegationsRequest));
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
            $this->filesystem->remove('var/export'.$exportDelegationsRequest->getId());
        }
        $this->filesystem->mkdir('var/export'.$exportDelegationsRequest->getId());

        $validators = $this->validatorCosmosDirectoryClient->getChain($exportDelegationsRequest->getNetwork());

        if ($exportDelegationsRequest->getApiClient()) {
            $cosmosClient = new CosmosClient($exportDelegationsRequest->getApiClient(), 'manual');
        } else {
            $cosmosClient = $this->cosmosClientFactory->createClient($exportDelegationsRequest->getNetwork());
        }

        $limit = 1000;
        foreach ($validators as $validator) {
            $page = 1;
            $offset = 0;
            $lastDelegator = null;
            while (true) {
                $delegations = $cosmosClient->getValidatorDelegations($validator['address'], $limit, $offset);
                if (\count($delegations->getDelegationResponses()) === 0) {
                    break;
                }
                if ($delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress() === $lastDelegator) {
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

        return 'var/export/'.$exportDelegationsRequest->getId();
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
}