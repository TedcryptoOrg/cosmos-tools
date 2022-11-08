<?php

namespace App\MessageHandler\Tools;

use App\Enum\Export\ExportStatusEnum;
use App\Message\Tools\ExportDelegationMessage;
use App\Service\Tools\ExportDelegationsMailer;
use App\Service\Tools\ExportDelegationsManager;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExportDelegationMessageHandler
{
    private ManagerRegistry $managerRegistry;

    private EntityManagerInterface $entityManager;

    private ExportDelegationsManager $exportDelegationsManager;

    private ExportDelegationsMailer $exportDelegationsMailer;

    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager, ExportDelegationsManager $exportDelegationsManager, ExportDelegationsMailer $exportDelegationsMailer, LoggerInterface $logger)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityManager = $entityManager;
        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->exportDelegationsMailer = $exportDelegationsMailer;
        $this->logger = $logger;
    }

    public function __invoke(ExportDelegationMessage $message): void
    {
        $exportDelegationRequest = $this->exportDelegationsManager->find($message->getExportDelegationsRequestId());
        $this->logger->info(sprintf('[Request: %s] Processing export delegation request', $exportDelegationRequest->getId()));

        try {
            $this->exportDelegationsManager->flagProcessing($exportDelegationRequest, ExportStatusEnum::PROCESSING);

            $this->logger->info(sprintf('[Request: %s] Exporting delegations...', $exportDelegationRequest->getId()));
            $this->exportDelegationsManager->fetchAndSave($exportDelegationRequest);

            $this->exportDelegationsManager->flagAsDone($exportDelegationRequest);
            $this->logger->info(sprintf('[Request: %s] Exported', $exportDelegationRequest->getId()));

            $this->exportDelegationsMailer->sendDoneEmail($exportDelegationRequest);
        } catch (\Exception $exception) {
            $this->pingConnection();
            $this->exportDelegationsManager->flagAsErrored($exportDelegationRequest, $exception->getMessage());
            $this->exportDelegationsMailer->sendErrorEmail($exportDelegationRequest);
        }
    }

    private function pingConnection(): void
    {
        $entityManager = $this->entityManager;
        $connection = $entityManager->getConnection();

        try {
            $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL());
        } catch (DBALException) {
            $connection->close();
            $connection->connect();
        }

        if (!$entityManager->isOpen()) {
            $this->managerRegistry->resetManager(null);
        }
    }
}