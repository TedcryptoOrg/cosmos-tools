<?php

namespace App\Service\Tools;

use App\Entity\Export\Delegation;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Message\Tools\ExportDelegationMessage;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\CosmosDirectory\ValidatorCosmosDirectoryClient;
use App\Service\Export\ExportProcessManager;
use App\Utils\MemoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ExportDelegationsManager
{
    private EntityManagerInterface $entityManager;

    private MessageBusInterface $bus;

    private DelegationFetcherManager $delegationFetcherManager;

    public function __construct(EntityManagerInterface $entityManager, DelegationFetcherManager $delegationFetcherManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->delegationFetcherManager = $delegationFetcherManager;
        $this->bus = $bus;
    }

    public function find(int $id): ?ExportDelegationsRequest
    {
        return $this->entityManager->getRepository(ExportDelegationsRequest::class)->find($id);
    }

    public function createRequest(array $formData): ExportDelegationsRequest
    {
        $exportDelegationsRequest = new ExportDelegationsRequest();
        $exportDelegationsRequest
            ->setApiClient($formData['custom_api_server'] ?: $formData['api_client'])
            ->setEmail($formData['email'] ?: null)
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

    public function flagAsDone(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::DONE)
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

    public function fetchAndSave(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        if ($exportDelegationsRequest->getStatus() === ExportStatusEnum::DONE) {
            throw new \Exception('This request has already been processed');
        }

        $this->delegationFetcherManager->fetch($exportDelegationsRequest);
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