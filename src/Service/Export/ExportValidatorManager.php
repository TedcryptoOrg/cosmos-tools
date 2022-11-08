<?php

namespace App\Service\Export;

use App\Entity\Export\Validator;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Event\ExportValidatorCompletedEvent;
use App\Service\Tools\DelegationFetcherManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class ExportValidatorManager
{
    private EntityManagerInterface $entityManager;

    private DelegationFetcherManager $delegationFetcherManager;

    private LoggerInterface $logger;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, DelegationFetcherManager $delegationFetcherManager, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->delegationFetcherManager = $delegationFetcherManager;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $validatorId): ?Validator
    {
        return $this->getRepository()->find($validatorId);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Validator::class);
    }

    public function flagProcessing(Validator $exportValidator): void
    {
        $exportValidator
            ->setStatus(ExportStatusEnum::PROCESSING)
            ->setErrorMessage(null)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function flagAsError(Validator $exportValidator, string $error): void
    {
        $exportValidator
            ->setStatus(ExportStatusEnum::ERROR)
            ->setErrorMessage($error)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();
    }

    public function flagAsDone(Validator $exportValidator): void
    {
        $exportValidator
            ->setStatus(ExportStatusEnum::DONE)
            ->setIsCompleted(true)
            ->setCompletedAt(new \DateTime())
            ->setErrorMessage(null)
            ->setUpdatedAt(new \DateTime())
        ;
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new ExportValidatorCompletedEvent($exportValidator));
    }

    public function fetchAndSave(ExportDelegationsRequest $exportDelegationsRequest, Validator $validator): void
    {
        // Remove previous one if any
        if ($validator->getDelegations()) {
            $this->logger->info(sprintf('[Request: %s][Validator: %s]Removing previous delegations', $exportDelegationsRequest->getId(), $validator->getValidatorName()));
            $validator->setDelegations([]);
            $this->entityManager->flush();
        }

        $this->delegationFetcherManager->fetch($exportDelegationsRequest, $validator);
    }
}