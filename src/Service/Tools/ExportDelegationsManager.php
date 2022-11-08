<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Message\Export\FetchValidatorDelegationsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ExportDelegationsManager
{
    private EntityManagerInterface $entityManager;

    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
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

    public function flagProcessing(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        $exportDelegationsRequest
            ->setStatus(ExportStatusEnum::PROCESSING)
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

        foreach ($exportDelegationsRequest->getExportProcess()->getValidators() as $validator) {
            if ($validator->getStatus() === ExportStatusEnum::DONE) {
                continue;
            }

            $this->bus->dispatch(new FetchValidatorDelegationsMessage($exportDelegationsRequest->getId(), $validator->getId()));
        }
    }
}