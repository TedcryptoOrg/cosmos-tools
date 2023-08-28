<?php

namespace App\MessageHandler\Export;

use App\Enum\Export\ExportStatusEnum;
use App\Message\Export\FetchValidatorDelegationsMessage;
use App\Service\Export\ExportValidatorManager;
use App\Service\Tools\ExportDelegationsManager;
use App\Utils\EntityManagerUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class FetchValidatorDelegationsMessageHandler
{
    public function __construct(private readonly ExportValidatorManager $exportValidatorManager, private readonly ExportDelegationsManager $exportDelegationsManager, private readonly EntityManagerUtil $entityManagerUtil, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(FetchValidatorDelegationsMessage $message): void
    {
        $exportValidator = $this->exportValidatorManager->find($message->getExportValidatorId());
        if ($exportValidator === null) {
            throw new UnrecoverableMessageHandlingException('Export validator not found');
        }
        $exportDelegationRequest = $this->exportDelegationsManager->find($message->getExportDelegationRequestId());
        if ($exportDelegationRequest === null) {
            throw new UnrecoverableMessageHandlingException('Export delegation request not found');
        }

        // Flag so the user knows that something is coming...
        if (ExportStatusEnum::PENDING === $exportDelegationRequest->getStatus()) {
            $this->exportDelegationsManager->flagProcessing($exportDelegationRequest);
        }

        $this->logger->info(sprintf('[Request: %s][Validator: %s] Fetching delegations', $exportDelegationRequest->getId(), $exportValidator->getValidatorName()));
        try {
            $this->exportValidatorManager->flagProcessing($exportValidator);

            $this->exportValidatorManager->fetchAndSave($exportDelegationRequest, $exportValidator);

            $this->exportValidatorManager->flagAsDone($exportValidator);

            $this->logger->info(sprintf('[Request: %s][Validator: %s] Exported', $exportDelegationRequest->getId(), $exportValidator->getValidatorName()));
        } catch (\Exception $exception) {
            $this->entityManagerUtil->pingConnection();

            $this->exportDelegationsManager->flagAsErrored($exportDelegationRequest, $exception->getMessage());
            $this->exportValidatorManager->flagAsError($exportValidator, $exception->getMessage());
        }
    }
}
